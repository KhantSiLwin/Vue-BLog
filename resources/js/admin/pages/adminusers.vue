<template>
		<div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<div class="d-flex justify-content-between align-items-center mb-2">
					<p class="_title0 m-0"><Icon type="md-priceadminusers" />  Admin Users</p>
					<button v-if="isWritePermitted" @click="addModal=true" class="btn btn-outline-primary btn-sm"><Icon type="md-add-circle" />  Add Admin User</button>
				</div>
					<div class="_overflow _table_div">
					<table class="table table-bordered table-hover shadow-sm table-striped">
								<!-- TABLE TITLE -->
							<thead class="bg-primary text-white">
							<tr class="text-center">
								<th>Id</th>
								<th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
								<th>Created at</th>
								<th>Action</th>
							</tr>
							</thead>
								<!-- TABLE TITLE -->

									<!-- {{roles[adminuser.role_id].roleName}} -->
								<!-- ITEMS -->
							<tbody v-if="adminusers[0]">
							<tr class="text-center" v-for="(adminuser,i) in adminusers" :key="i">
								<td>{{id=i+1}}</td>
								<td>{{adminuser.fullName}}</td>
                                <td>{{adminuser.email}}</td>
									<!-- <span v-for="(y,i) in roles" :key="i">
										<td v-if="y.id==adminuser.role_id">{{y.roleName}}</td>
									</span> -->
								<td>{{getRoleName(adminuser.role_id)}}</td>
								<td>{{adminuser.created_at}}</td>
								<td>
									<button v-if="isUpdatePermitted" class="btn btn-sm btn-outline-info" @click="showEdit(adminuser,i)" type="button"><Icon type="md-create" /></button>
									<button v-if="isDeletePermitted" class="btn btn-sm btn-outline-danger"  @click="showDelete(adminuser,i)" type="button"><Icon type="ios-trash" /></button>

								</td>
							</tr>
							</tbody>
								<!-- ITEMS -->

							<tbody v-else>
							<tr class="">
								<td v-if="apiloading" colspan="6" class="text-center"><img src="images/loading.gif" style="width:35px;height:35px;margin:auto;" alt=""></td>
								<td  v-else colspan="6" class="text-center">There is no adminsers.. </td>
							</tr>
							</tbody>

						</table>
					</div>

					<!-- adminuser adding  model -->
					<Modal 
						v-model="addModal"
						title="Add Admin User"
						:mask-closable="false"
						:closable="false"
						width="360px"
						>
						  <!-- <Input v-model="data.adminuserName" @keyup.enter="addTag" placeholder="Enter Admin User Name..." style="width: 300px" /> -->
						<div class="form-group">
                            	<input type="text" class="form-control" v-model="data.fullName" @keyup.enter="addTag" style="width: 300px" placeholder="Enter Full Name...">
                        </div>
                        <div class="form-group">
                            	<input type="text" class="form-control" v-model="data.email" @keyup.enter="addTag" style="width: 300px" placeholder="Enter Email...">
                        </div>
                        <div class="form-group">
                            	<input type="password" class="form-control" v-model="data.password" @keyup.enter="addTag" style="width: 300px" placeholder="Enter Password...">
                        </div>
                        <div class="form-group">
                            <Select v-model="data.role" style="width:301px" placeholder="Select User Type">
                              
                                <Option v-for="(r,i) in roles" :key='i' :value="r.id">{{r.roleName}}</Option>
                              
                            </Select>
                        </div>
						<div slot="footer">
							<Button type="default" @click="closeModalClearData">Close</Button>
							<Button type="primary" @click="addAdminUser" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding' : 'Add Admin User' }}</Button>
						</div>

					</Modal>

					<Modal 
						v-model="editModal"
						title="Edit Admin User"
						:mask-closable="false"
						:closable="false"
						width="360px"
						>
						  <!-- <Input v-model="editData.adminuserName" placeholder="Enter Admin User Name..." style="width: 300px" /> -->
						<!-- <input type="text" class="form-control" v-model="editData.adminuserName" @keyup.enter="editTag" style="width: 300px" placeholder="Enter Admin User Name..."/> -->

                        <div class="form-group">
                            	<input type="text" class="form-control" v-model="editData.fullName" @keyup.enter="editAdminUser" style="width: 300px" placeholder="Enter Full Name...">
                        </div>
                        <div class="form-group">
                            	<input type="text" class="form-control" v-model="editData.email" @keyup.enter="editAdminUser" style="width: 300px" placeholder="Enter Email...">
                        </div>
                        <div class="form-group">
                            	<input type="password" class="form-control" v-model="editData.password" @keyup.enter="editAdminUser" style="width: 300px" placeholder="Enter Password...">
                        </div>
                        <div class="form-group">
                            <Select v-model="editData.role"  style="width:301px" placeholder="Select User Type">
                                <Option v-for="(r,i) in roles" :key='i' :value="r.id">{{r.roleName}}</Option>
                              
                            </Select>
                        </div>

						<div slot="footer">
							<Button type="default" @click="editModal=false">Close</Button>
							<Button type="primary" @click="editAdminUser" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Editing' : 'Edit Admin User' }}</Button>
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
					fullName: '',
                    email:'',
                    password:'',
                    role: null,
				},
				apiloading:true,
				addModal : false,
				isAdding : false,
				editModal : false,
				isEditing : false,
				adminusers:[],
				editData:{
                    id:'',
					fullName: '',
                    email:'',
                    password:'',
                    role: null,
				},
				index: -1,
				deletingIndex:-1,
				roles: []
			}
		},

		methods: {
		async addAdminUser(){
				if(this.data.fullName.trim()=='') return this.e('Admin User name is Required')
                if(this.data.email.trim()=='') return this.e('Email name is Required')
                if(this.data.password.trim()=='') return this.e('Password is Required')
                // if(this.data.role.trim()=='') return this.e('User Type is Required')
				this.isAdding=true
				const res = await this.callApi('post','app/create_adminuser',this.data)
				if(res){
					this.isAdding=false
					if(res.status==201){
					this.adminusers.unshift(res.data)
					this.s('Admin User has been added successfully!')
					this.addModal=false
					this.data.fullName=''
                    this.data.email=''
                    this.data.password=''
                    this.data.role=''

				}else{
					if(res.status==422){
						for(let i in res.data.errors){
                            this.e(res.data.errors[i][0])
                        }
                      
					}else{
						this.swr()
					}
					
				}
				}
			},

			closeModalClearData(){

				this.addModal=false
				this.data={
					fullName: '',
                    email:'',
                    password:'',
                    role: null,
				}

			},

			async editAdminUser(){
                if(this.editData.fullName.trim()=='') return this.e('Admin User name is Required')
                if(this.editData.email.trim()=='') return this.e('Email name is Required')
                // if(this.data.role.trim()=='') return this.e('User Type is Required')
                this.isEditing=true
				const res = await this.callApi('post','app/edit_adminuser',this.editData)
				if(res){
					this.isEditing=false
					if(res.status==200){
					 
					this.adminusers[this.index].fullName=this.editData.fullName
                    this.adminusers[this.index].email=this.editData.email
                    this.adminusers[this.index].role_id=this.editData.role
					// if(this.editData.id==this.getUpdateUser.id){
					// 	 this.$store.commit('setUpdateUserAdminEdit',this.editData)
					// 	 let index = this.roles.findIndex(role => role.id == this.editData.role)
					// 	 this.$store.commit('setUserPermission',JSON.parse(this.roles[index].permission))	  
					// }
					this.s('Admin User has been edited successfully!')
					this.editModal=false
					
				}else{
					if(res.status==422){
					for(let i in res.data.errors){
                        this.e(res.data.errors[i][0])
                    }
					}else{
						this.swr()
					}
					
				}
				}
			},
			showEdit(adminuser,index){
				let obj = {
					id : adminuser.id,
					fullName:adminuser.fullName,
                    email:adminuser.email,
                    password:'',
                    role:adminuser.role_id,
				}
				this.editData = obj
				this.editModal =true
				this.index = index
			},
			
			
			showDelete(adminuser,index){
				const deleteModalObj = {

					deleteModal : true,
					deleteUrl : 'app/delete_adminuser',
					data : adminuser,
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

			getRoleName(role_id){
				for(let y in this.roles){
					if(this.roles[y].id == role_id){
						return this.roles[y].roleName
					
					}
                }
			}
			
			
		},

		async created(){
			const[res,resRole] = await Promise.all([
				this.callApi('get','app/get_adminusers'),
				this.callApi('get','app/get_roles')
			])

		
			
				if(res.status=200){
					this.apiloading=false
					this.adminusers =res.data
				}else{
					this.swr()
				}
				if(resRole.status=200){
					this.roles =resRole.data
				}else{
					this.swr()
				}

		},
		components : {
			deleteModal
		},
		computed : {
			...mapGetters(['getDeleteModalObj','getUpdateUser'])
		},
		watch: {
			getDeleteModalObj(obj){
				if(obj.isDeleted){
					this.adminusers.splice(this.deletingIndex,1)
				}
			}
		}
        
		

    }
</script>

<style lang="scss" scoped>

</style>