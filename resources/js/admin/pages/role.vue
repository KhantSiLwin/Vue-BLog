<template>
		<div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<div class="d-flex justify-content-between align-items-center mb-2">
					<p class="_title0 m-0"><Icon type="md-build" />  Role Management</p>
					<button v-if="isWritePermitted" @click="addModal=true" class="btn btn-outline-primary btn-sm"><Icon type="md-add-circle" />  Add Role</button>
				</div>
					<div class="_overflow _table_div">
					<table class="table table-bordered table-hover shadow-sm table-striped">
								<!-- TABLE TITLE -->
							<thead class="bg-primary text-white">
							<tr class="text-center">
								<th>Id</th>
								<th>Role Name</th>
								<th>Created at</th>
								<th>Action</th>
							</tr>
							</thead>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tbody v-if="roles[0]">
							<tr class="text-center" v-for="(role,i) in roles" :key="i">
								<td>{{id=i+1}}</td>
								<td>{{role.roleName}}</td>
								<td>{{role.created_at}}</td>
								<td>
									<button v-if="isUpdatePermitted" class="btn btn-sm btn-outline-info" @click="showEdit(role,i)" type="button"><Icon type="md-create" /></button>
									<button v-if="isDeletePermitted" class="btn btn-sm btn-outline-danger"  @click="showDelete(role,i)" type="button"><Icon type="ios-trash" /></button>

								</td>
							</tr>
							</tbody>
								<!-- ITEMS -->

							<tbody v-else>
							<tr class="">
								<td v-if="apiloading" colspan="4" class="text-center"><img src="images/loading.gif" style="width:35px;height:35px;margin:auto;" alt=""></td>
								<td  v-else colspan="4" class="text-center">There is no role.. </td>
							</tr>
							</tbody>

						</table>
					</div>

					<!-- role adding  model -->
					<Modal 
						v-model="addModal"
						title="Add Role"
						:mask-closable="false"
						:closable="false"
						width="360px"
						>
						  <!-- <Input v-model="data.roleName" @keyup.enter="addRole" placeholder="Enter Role Name..." style="width: 300px" /> -->
							<input type="text" class="form-control" v-model="data.roleName" @keyup.enter="addRole" style="width: 300px" placeholder="Enter Role Name..." autofocus>
						<div slot="footer">
							<Button type="default" @click="closeModalClearData">Close</Button>
							<Button type="primary" @click="addRole" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding' : 'Add Role' }}</Button>
						</div>
					</Modal>

					<Modal 
						v-model="editModal"
						title="Edit Role"
						:mask-closable="false"
						:closable="false"
						width="360px"
						>
						  <!-- <Input v-model="editData.roleName" placeholder="Enter Role Name..." style="width: 300px" /> -->
						<input type="text" class="form-control" v-model="editData.roleName" @keyup.enter="editRole" style="width: 300px" placeholder="Enter Role Name..."/>

						<div slot="footer">
							<Button type="default" @click="editModal=false">Close</Button>
							<Button type="primary" @click="editRole" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Editing' : 'Edit Role' }}</Button>
						</div>
					</Modal>

						<deleteModal></deleteModal>


				</div>
			</div>
		</div>

</template>

<script>
import deleteModal from '../components/deleteModal'
import {mapGetters} from 'vuex'
    export default {

		data(){
			return{
				data: {
					roleName: ''
				},
				apiloading:true,
				addModal : false,
				isAdding : false,
				editModal : false,
				isEditing : false,
				roles:[],
				resources:[
					{resourceName:'Dashboard',iconName:"md-analytics",read:false,write:false,update:false,delete:false,name:'/'},
					{resourceName:'Tags',iconName:"ios-pricetags",read:false,write:false,update:false,delete:false,name:'tags'},
					{resourceName:'Category',iconName:"md-git-branch",read:false,write:false,update:false,delete:false,name:'category'},
					{resourceName:'Admin Users',iconName:"md-people",read:false,write:false,update:false,delete:false,name:'adminusers'},
					{resourceName:'Role',iconName:"md-bookmark",read:false,write:false,update:false,delete:false,name:'role'},
					{resourceName:'Assign Permission',iconName:"md-build",read:false,write:false,update:false,delete:false,name:'assignRole'},
				],
				editData:{
					roleName: ''
				},
				index: -1,
				deletingIndex:-1
			}
		},

		methods: {
		async addRole(){
				if(this.data.roleName.trim()=='') return this.e('Role name is Reqired')
				let resource = JSON.stringify(this.resources)
				this.isAdding=true
				const res = await this.callApi('post','app/create_role',{roleName : this.data.roleName,permission:resource})
				if(res){
					this.isAdding=false
					if(res.status==201){
					this.roles.unshift(res.data)
					this.s('Role has been added successfully!')
					this.addModal=false
					this.data.roleName=''
				}else{
					if(res.status==422){
						if(res.data.errors.roleName){
							this.e(res.data.errors.roleName[0])
						}
					}else{
						this.swr()
					}
					
				}
				}
			},

			closeModalClearData(){

				this.addModal=false
				this.data.roleName=''

			},


			async editRole(){
				if(this.editData.roleName.trim()=='') return this.e('Role name is Reqired')
			    this.isEditing=true
				const res = await this.callApi('post','app/edit_role',this.editData)
				if(res){
					this.isEditing=false
					if(res.status==200){
					 
					this.roles[this.index].roleName=this.editData.roleName
					this.s('Role has been edited successfully!')
					this.editModal=false
					
				}else{
					if(res.status==422){
						if(res.data.errors.roleName){
							this.e(res.data.errors.roleName[0])
						}
					}else{
						this.swr()
					}
					
				}
				}
			},
			showEdit(role,index){
				let obj = {
					id : role.id,
					roleName:role.roleName
				}
				this.editData = obj
				this.editModal =true
				this.index = index
			},
			
			
			showDelete(role,index){
				const deleteModalObj = {

					deleteModal : true,
					deleteUrl : 'app/delete_role',
					data : role,
					deletingIndex:index,
					isDeleted :false
        	}
				this.deletingIndex=index
				this.$store.commit('setDeletingModalObj',deleteModalObj)
				// console.log('delete method called')
				// 	console.log(deleteModalObj)
				// this.deleteItem= deleteObj
				// this.deleteModal =true
				// this.index = index
			},

			
			
		},

		async created(){

			const res = await this.callApi('get','app/get_roles')
				if(res.status=200){
					this.apiloading=false
					this.roles =res.data
				}else{
					this.swr()
				}

		},
		components : {
			deleteModal
		},                                                                                              
		computed : {
			...mapGetters(['getDeleteModalObj'])
		},
		watch: {
			getDeleteModalObj(obj){
				if(obj.isDeleted){
					this.roles.splice(this.deletingIndex,1)
					console.log(this.deletingIndex);
				}	
			}
		}
        
		

    }
</script>

<style lang="scss" scoped>

</style>