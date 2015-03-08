function getControls() {
 var tbls;
 var ctrls;
 var cl;
 var i;
 ctrls = [];
 
 tbls = document.getElementsByTagName('table');
 for(i = 0; i < tbls.length; i++) {
  cl = tbls[i].getAttribute('class');
  if (cl == 'control_panel') { 
   ctrls.push(tbls[i]);
  }                  
 }
 return ctrls;                 
}

function getParentByTagName(e,tagName) {
 var p;
 var tname;
 
 p = e;                                    
 while (typeof(p.parentNode) != 'undefined') {
  p = p.parentNode;
  if (p.nodeName.toUpperCase() == tagName.toUpperCase()) {
   return p; 
  }
 }
 return null;
}

function doTask(task,ctl_form) {                  
 f = document.getElementById(ctl_form);
 if (f != null) {
  f.task.value = task;
  f.submit();
 }
 return false;
}

function arrayHas(a,v) {
 var i;                  
 for(i=0;i < a.length;a++) {
  if (a[i] == v) return true;
 }
 return false;
}

function cleanControl(ctl,ctl_form) {
 var i;
 var btns;
 var sel;
 var tmpa;
 var nn;
 var sel;
 var i2;
 var ln;
 var task;
 var rm_list;
 
 rm_list = [];
                                                       
 btns = ctl.getElementsByTagName('li');
 for(i=0;i < btns.length;i++) {
  //alert(btns[i].getAttribute('class'));
  
  if (btns[i].getAttribute('class') == 'submit') {
   rm_list.push(btns[i]);
  } else {                    
   tmpa = [];
   tmpa = btns[i].getElementsByTagName('input');
   sel = null;
   for(i2 = 0; i2 < tmpa.length; i2++) {
    if (tmpa[i2].getAttribute('name') == 'task') {
     sel = tmpa[i2];
     task = tmpa[i2].getAttribute('value');
    }
   }
   if (sel != null) {
    
    ln = getParentByTagName(sel,'a');
    if (ln !== null) {                     
     ln.onclick = new Function('doTask(\''+task+'\',\''+ctl_form+'\');');
    }                     
    rm_list.push(sel);                        
   }
      
  }                  
 }
 
 for(i=0;i<rm_list.length;i++) {
  rm_list[i].parentNode.removeChild(rm_list[i]);
 }
}

function columnToggleCheckAll(colid) {

 var colmarker;
 var colptr = 0;
 var p;
 var tbl;
 var tbl_body;
 var state;
 var cl;
 var c;     
 var rows;
 var row;
 var i;
 var i2;
 var i3;
 
 colmarker = document.getElementById(colid);
 if (colmarker == null) {
  return false;
 }
 
 p = colmarker;     
 while(p) {
  p = p.previousSibling;
  if (p && (p.nodeType == 1)) {
   colptr++;
  }     
 }
 
 cl = colmarker.getElementsByTagName('input');
 
 state = null;
 
 for(i=0;i < cl.length; i++) {
  c = cl[i];
  if (c.getAttribute('type') == 'checkbox') {
   state = c.checked;
  }
 }                    
 
 tbl = getParentByTagName(colmarker,'table');
 cl = tbl.getElementsByTagName('tbody');
 tbl_body = cl[0];
 
 rows = [];
 
 i = 0;
 for (i2=0; i2 < tbl_body.childNodes.length; i2++) {
  p = tbl_body.childNodes[i2];
  if (p.nodeType == 1) {
   rows[i++] = p;      
  }  
 }
           
 for(i=0;i < rows.length;i++) {
  row = rows[i];
  i3 = 0;
  c = null;
  
  for(i2 = 0; i2 < row.childNodes.length; i2++) {
   if (row.childNodes[i2].nodeType == 1) {       
    if (i3 == colptr) {
     c = row.childNodes[i2];
     i2 = row.childNodes.length;
    }
    i3++;       
   }
  }
  
  if (c) {
   cl = c.getElementsByTagName('input');
   for(i3=0;i3 < cl.length;i3++) {
    if (cl[i3].getAttribute('type') == "checkbox") {
     cl[i3].checked = state;
    }       
   }      
  }      
 }
 
 return true;
}


function cleanAllControls() {                 
 var ctrls;
 var i;
 var f;
 var ctrl_forms;
 var t;
 ctrl_forms = [];
                   
 ctrls = getControls();                  
 for(i=0;i<ctrls.length;i++) {
  f = getParentByTagName(ctrls[i],'form');
  fid = null;
  if (f != null) {
   fid = f.getAttribute('id');
  }                                      
  if (fid != null) {
   if (!arrayHas(ctrl_forms,fid)) {
    ctrl_forms.push(fid);                     
   }
   cleanControl(ctrls[i],fid);
  }
 }
                   
 for(i=0;i<ctrl_forms.length;i++) {                   
  f = document.getElementById(ctrl_forms[i]);                   
  if (f != null) {
   t = document.createElement('input');
   t.setAttribute('type','hidden');
   t.setAttribute('name','task');
   t.setAttribute('value','');
   f.appendChild(t);
  } 
 }                                    
}

 function doMoveTask(formName,task,identName,id,dirName,dir) {
  var frm;
  var eident;
  var edir;
  var il;
  var i;
  var n;
  
  eident = false;
  edir = false;
    
  frm = document.getElementById(formName);
  
  if (!frm) {
   alert('Missing form: '+formName);
  }
  
  il = frm.getElementsByTagName('input');
  
  for(i=0;i < il.length; i++) {
   n = il[i].getAttribute('name');
   if (n == identName) {
    eident = il[i];
   }
   if (n == dirName) {
    edir = il[i];
   }
    
  }
  
  if (!eident) {
   alert('Missing move identity field: ' + identName);
   return false;
  }

  if (!edir) {
   alert('Missing move direction field: ' + dirName);
   return false;
  }
  
  frm.task.value = task;
  eident.value = id;
  edir.value = dir;
  frm.submit();  
 }
 
 function addMoveButtons(frmId,task,ident,orderFieldName,identFieldName,dirFieldName) {
  var frm;
  var il;
  var ib;
  var i;     
  var e1;
  var e2;
  var p;
  var j;
       
  
  frm = document.getElementById(frmId);
  if (!frm) {
   return false;
  }
  
  il = frm.getElementsByTagName('input');
  ib = false;
            
  for(i=0;i < il.length; i++) {
   if (il[i].getAttribute('name') == orderFieldName) {
    ib = il[i];
    i = il.length;
   }
  }
  
  if (!ib) {
   alert('Missing Order Input: ' + orderFieldName);
   return false;
  }
  
  p = ib.parentNode;
  if (!p) {
   return false;
  }
  
  // Gen Up
  
  e1 = document.createElement('a');
  e2 = document.createElement('span');
  e1.setAttribute('href','#');
  e1.setAttribute('class','up');
  j = 'javascript:doMoveTask(\''+frmId+'\',\''+task+'\',\''+identFieldName+'\',\''+ident+'\',\''+dirFieldName+'\',-1)';
  e1.setAttribute('onclick',j);
  e1.appendChild(e2);
  p.appendChild(e1);
  
  // Gen Down

  e1 = document.createElement('a');
  e2 = document.createElement('span');
  e1.setAttribute('href','#');
  e1.setAttribute('class','down');     
  j = 'javascript:doMoveTask(\''+frmId+'\',\''+task+'\',\''+identFieldName+'\',\''+ident+'\',\''+dirFieldName+'\',1)';
  e1.setAttribute('onclick',j);
  e1.appendChild(e2);
  p.appendChild(e1);
           
 }
    
