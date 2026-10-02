<?php
namespace Tests\Regression;
use App\Models\Review;
class ReviewRegressionTest extends RegressionTestCase
{
    public function test_purchaser_review_persists_and_updates_rating(): void
    {
        $u=$this->customer();$p=$this->product();$this->order($u,'completed','cash',$p);$this->actingAs($u);
        $this->postJson("/products/{$p->id}/reviews",['product_id'=>$p->id,'rating'=>4,'comment'=>'REGRESSION excellent dummy appliance.'])->assertStatus(201);
        $this->assertDatabaseHas('reviews',['product_id'=>$p->id,'user_id'=>$u->id,'rating'=>4]);
        $this->assertEquals(4,$p->fresh()->rating);$this->assertEquals(1,$p->fresh()->rating_count);
    }
    public function test_non_purchaser_cannot_review(): void
    {
        $p=$this->product();$this->actingAs($this->customer());
        $this->postJson("/products/{$p->id}/reviews",['product_id'=>$p->id,'rating'=>5,'comment'=>'REGRESSION non purchaser review.'])->assertStatus(403);
        $this->assertDatabaseMissing('reviews',['product_id'=>$p->id]);
    }
    public function test_duplicate_review_is_rejected(): void
    {
        $u=$this->customer();$p=$this->product();$this->order($u,'completed','cash',$p);$this->actingAs($u);
        $body=['product_id'=>$p->id,'rating'=>5,'comment'=>'REGRESSION duplicate review.'];
        $this->postJson("/products/{$p->id}/reviews",$body)->assertStatus(201);
        $this->postJson("/products/{$p->id}/reviews",$body)->assertStatus(422);
        $this->assertSame(1,Review::where('product_id',$p->id)->where('user_id',$u->id)->count());
    }
    /** @dataProvider invalidRatings */
    public function test_rating_validation($rating): void
    {
        $p=$this->product();$this->actingAs($this->customer());
        $this->postJson("/products/{$p->id}/reviews",['product_id'=>$p->id,'rating'=>$rating,'comment'=>'REGRESSION validation review.'])->assertStatus(422)->assertJsonValidationErrors('rating');
    }
    public static function invalidRatings(): array {return [[0],[6],[-1],[2.5],['abc']];}
    public function test_other_customer_cannot_edit_review(): void
    {
        $u=$this->customer();$p=$this->product();
        $r=Review::forceCreate(['user_id'=>$u->id,'product_id'=>$p->id,'rating'=>4,'comment'=>'Original regression review','content'=>'Original regression review','user_name'=>$u->name]);
        $this->actingAs($this->customer());
        $this->putJson("/reviews/{$r->id}",['rating'=>1,'comment'=>'Unauthorised regression edit'])->assertStatus(403);
        $this->assertEquals(4,$r->fresh()->rating);
    }
}
