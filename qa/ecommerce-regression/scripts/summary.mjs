import fs from 'node:fs';
const file = process.argv[2] || 'reports/results.json';
if (!fs.existsSync(file)) { console.error('No completed report yet. Run the regression first.'); process.exit(1); }
const report = JSON.parse(fs.readFileSync(file, 'utf8'));
console.log(JSON.stringify({source:file,started:report.stats.startTime,passed:report.stats.expected,failed:report.stats.unexpected,skipped:report.stats.skipped,flaky:report.stats.flaky},null,2));
