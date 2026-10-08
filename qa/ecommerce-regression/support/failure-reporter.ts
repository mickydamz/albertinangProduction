import type { Reporter, TestCase, TestResult, TestStep } from '@playwright/test/reporter';
import { mkdirSync, writeFileSync } from 'node:fs';
import { join, relative } from 'node:path';
import { clean } from './evidence.js';
export default class FailureReporter implements Reporter {
  private steps = new Map<string, {title:string; location?:TestStep['location']; message:string}>();
  private failures: unknown[] = [];
  onStepEnd(test:TestCase,_result:TestResult,step:TestStep) {
    // Deepest error is reported first; keep its exact locator/source rather than the enclosing journey.
    if(step.error && !this.steps.has(test.id)) this.steps.set(test.id,{title:step.title,location:step.location,message:clean(step.error.message||'')});
  }
  onTestEnd(test:TestCase,result:TestResult) {
    if(result.status==='failed' || result.status==='timedOut' || result.status==='interrupted') {
      this.failures.push({test:test.titlePath().join(' > '),status:result.status,exactPoint:this.steps.get(test.id),
        source:test.location,errors:result.errors.map(e=>({message:clean(e.message||''),stack:clean(e.stack||'')})),
        evidence:result.attachments.map(a=>({name:a.name,path:a.path,contentType:a.contentType}))});
    }
    this.steps.delete(test.id);
  }
  onEnd() {
    const folder=process.env.REPORT_DIR||'reports'; mkdirSync(folder,{recursive:true});
    writeFileSync(join(folder,'failures.json'),JSON.stringify(this.failures,null,2));
    const records=this.failures as Array<{test:string;status:string;exactPoint?:{title:string;location?:{file:string;line:number};message:string};source:{file:string;line:number};errors:Array<{message:string}>;evidence:Array<{name:string;path?:string}>}>;
    writeFileSync(join(folder,'FAILURES.md'),records.length ? records.map((r,i)=>`## ${i+1}. ${r.test}\n\nStatus: ${r.status}\n\nExact point: ${r.exactPoint?.title||'Setup or interrupted run'}\n\nSource: ${r.exactPoint?.location?.file||r.source.file}:${r.exactPoint?.location?.line||r.source.line}\n\n${r.errors.map(e=>e.message.replace(/\u001b\[[0-9;]*m/g,'')).join('\n')}\n\nEvidence:\n\n${r.evidence.filter(a=>a.path).map(a=>`- [${a.name}](${relative(folder,a.path!)})`).join('\n')}`).join('\n\n') : 'No failed tests in this run. Check results.json for skipped and completed test counts.\n');
  }
}
