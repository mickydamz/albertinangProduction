<?php
namespace Tests\Regression;
class LegacyDepositNavigationTest extends RegressionTestCase {
 public function test_legacy_deposit_addresses_require_login_then_return_to_account(){
  foreach(['/my-deposits','/deposit-history','/deposit-create'] as $url)$this->get($url)->assertRedirect('/login');
  $this->actingAs($this->customer());foreach(['/my-deposits','/deposit-history','/deposit-create'] as $url)$this->get($url)->assertRedirect('/account');
 }
 public function test_settings_does_not_offer_deposit_feature(){
  $this->actingAs($this->customer())->get('/settings')->assertOk()->assertDontSee('Deposit History')->assertDontSee('/my-deposits',false)->assertDontSee('/deposit-create',false);
 }
}
