import {existsSync,mkdirSync,renameSync} from 'node:fs';
import {join} from 'node:path';
const folder=process.env.REPORT_DIR||'reports';
const files=['artifacts','html','results.json','junit.xml','failures.json','FAILURES.md'];
const present=files.filter(f=>existsSync(join(folder,f)));
if(present.length){
 const archive=join(folder,'archive',new Date().toISOString().replace(/[:.]/g,'-'));
 mkdirSync(archive,{recursive:true});
 for(const file of present) renameSync(join(folder,file),join(archive,file));
}
