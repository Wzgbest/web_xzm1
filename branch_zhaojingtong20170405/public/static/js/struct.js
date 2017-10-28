function Structure(dep_stf,dep_dep,stf_name) {
	// body...
	this.d_s = JSON.parse(dep_stf);
	this.d_d = JSON.parse(dep_dep);
	this.s_n = JSON.parse(stf_name);
	
	let self = this;

	//所有的部门
	this.getAllDep = function(){
		let arr = new Array();
		for(x in self.d_s){
			arr.push(x);
		}
		return arr;
	}
	this.all_dep = this.getAllDep();

	// 部门：人+子部门
	this.divide = function(){
		//存储最终结果
		let obj = new Object();
		//单个部门
		let d_sturcture = {
			peopel:[],
			department:[],
			p_id:0,

		}
		//初始化对象
		for(let x in self.d_d){
			obj[x] = self.d_d[x];
			obj[x].peopel = new Array();
			obj[x].department = new Array();
		}		
		//添加子部门
		for(let x in obj){
			let p = obj[x].pid;
			if(p!=0){
				obj[p].department.push(x);
			}
		}

		return obj;

	}
}