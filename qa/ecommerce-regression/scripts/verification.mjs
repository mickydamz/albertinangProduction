import {readFileSync,writeFileSync,existsSync} from 'node:fs';
import {dirname,relative,resolve,join} from 'node:path';
const inputs=process.argv.slice(2);
if(!inputs.length) throw new Error('Provide completed results.json files');
const cases=new Map();
for(const input of inputs){
 const report=JSON.parse(readFileSync(input,'utf8'));
 function visit(suite, parents=[]){
  const titles=[...parents,suite.title||''];
  for(const spec of suite.specs||[]) for(const test of spec.tests||[]) for(const result of test.results||[]){
   const caseId=spec.title.match(/E2E-[A-Z]+-\d+/)?.[0];if(!caseId) continue;
   const fulfilment=titles.join(' ').match(/Full ecommerce lifecycle (collection|delivery)/)?.[1];
   const id=fulfilment ? `${caseId} (${fulfilment})` : caseId;
   const at=result.startTime||report.stats.startTime;
   if(cases.has(id)&&cases.get(id).at>at) continue;
   const evidence=result.attachments?.find(a=>a.name==='failure-details')?.path;
   const detail=evidence&&existsSync(evidence)?JSON.parse(readFileSync(evidence,'utf8')):undefined;
   const trace=result.attachments?.find(a=>a.name==='trace')?.path;
   cases.set(id,{id,title:spec.title,status:result.status,at,report:resolve(input),evidence,trace,exactPoint:detail?.exactStep||result.errors?.[0]?.message?.replace(/\u001b\[[0-9;]*m/g,'').split('\n')[0]||'Completed'});
  }
  for(const nested of suite.suites||[]) visit(nested,titles);
 }
 for(const suite of report.suites) visit(suite);
}
const rows=[...cases.values()].sort((a,b)=>a.id.localeCompare(b.id));
const output=resolve('reports/VERIFICATION.md');
const link=p=>relative(dirname(output),resolve(p));
const counts=Object.fromEntries(['passed','failed','timedOut','skipped','interrupted'].map(status=>[status,rows.filter(x=>x.status===status).length]));
writeFileSync('reports/verification-summary.json',JSON.stringify({inputs,counts,scenarios:rows},null,2));
writeFileSync(output,`# Latest lifecycle verification\n\nLatest result for each scenario across the completed runs listed below. This is a per-scenario summary, not a claim that one uninterrupted full run passed.\n\n${rows.length} scenarios: ${counts.passed} passed, ${counts.failed+counts.timedOut+counts.interrupted} failed, ${counts.skipped} skipped.\n\n| Scenario | Result | Exact failure point | Evidence |\n|---|---|---|---|\n${rows.map(r=>`| ${r.id} | ${r.status} | ${r.exactPoint.replace(/\|/g,'/').replace(/\n/g,' ')} | ${r.evidence?`[Details](${link(r.evidence)})`:''} ${r.trace?`[Trace](${link(r.trace)})`:''} [Run](${link(join(dirname(r.report),'html/index.html'))}) |`).join('\n')}\n\nDeferred refund evidence[^refund-proof].\n\n[^refund-proof]: REFUND-PROOF-001 — fix later: order view lacks Paystack refund identifier. Refunded status alone does not verify the financial refund. Preserve current evidence.\n\nCompleted reports:\n\n${inputs.map(p=>`- [${p}](${link(p)})`).join('\n')}\n`);
console.log(JSON.stringify(counts));
