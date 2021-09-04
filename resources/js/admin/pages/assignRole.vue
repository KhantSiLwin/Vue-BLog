<template>
		<div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<div class="d-flex justify-content-between align-items-center mb-2">
					<p class="_title0 m-0"><Icon type="md-build" />  Role Management</p>
					 <Select v-model="data.id" @on-change="changeAssignRole" style="width:301px" placeholder="Select Role">
                                <Option v-for="(r,i) in roles" :key='i' :value="r.id">{{r.roleName}}</Option>
                              
                            </Select>
				</div>
					<div class="_overflow _table_div">
					<table class="table table-bordered table-hover shadow-sm table-striped">
								<!-- TABLE TITLE -->
							<thead class="bg-primary text-white">
							<tr class="text-center">
								<th>Resource Name</th>
								<th>Read</th>
								<th>Write</th>
								<th>Update</th>
								<th>Delete</th>
							</tr>
							</thead>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tbody v-if="resources[0]">
							<tr v-for="(r,i) in resources" :key="i" class="text-center" >
								<td>{{r.resourceName}}</td>
								<td><Checkbox v-model="r.read" :class="isUpdatePermitted ? 'disabled' : ''"></Checkbox></td>
								<td><Checkbox v-model="r.write"></Checkbox></td>
								<td><Checkbox v-model="r.update"></Checkbox></td>
								<td><Checkbox v-model="r.delete"></Checkbox></td>
							</tr>
							
							</tbody>
								<!-- ITEMS -->

							<tbody v-else>
							<tr class="">
								<td v-if="apiloading" colspan="5" class="text-center"><img src="images/loading.gif" style="width:35px;height:35px;margin:auto;" alt=""></td>
								<td  v-else colspan="5" class="text-center">There is no role.. </td>
							</tr>
							</tbody>

						</table>
					</div>

					<div class="text-right">
						<Button v-if="isUpdatePermitted" :loading="isSending" :disabled="isSending" @click="assignRoles" type="primary">Assign</Button>
					</div>
				</div>
			</div>
		</div>

</template>

<script>
import {mapGetters} from 'vuex'
    export default {

		data(){
			return{
				data: {
					
					roleName: '',
					id:null,
				},
				isSending: false,
				roles : [],
				resources:[],
				apiloading:true,
				
			}
		},

		methods: {
		
			async assignRoles(){
				let data = JSON.stringify(this.resources)
				const res = await this.callApi('post','app/assign_roles',{'permission':data,id:this.data.id})
				if(res.status=200){
					let index = this.roles.findIndex(role => role.id == this.data.id)
					this.roles[index].permission =data
					if(this.data.id==this.getUpdateUser.role_id){
						 this.$store.commit('setUserPermission',this.resources)
					}
					this.s('Role has been assigned successfully')
				}else{
					this.swr()
				}
			},

			changeAssignRole(){
				let index = this.roles.findIndex(role => role.id == this.data.id)
				this.resources= JSON.parse(this.roles[index].permission)
			},

			// updatePermit(){

			// 	if(!this.isUpdatePermitted){

			// 		return true
			// 		console.log(permittrue)
			// 	}else{

			// 	return false
			// console.log(permitfalse)
			// 	}
		// }

		},

		async created(){
			const res = await this.callApi('get','app/get_roles')
				if(res.status=200){
					this.apiloading=false
					this.roles =res.data
					
					if(res.data.length){
						this.data.id = res.data[0].id
						if(res.data[0].permission){
							this.resources =JSON.parse(res.data[0].permission)
						}
					}
				}else{
					this.swr()
				}

		},
		
        computed : {
			...mapGetters(['getUpdateUser'])
		},
		

    }
</script>

<style scoped>

	.disabled{
		cursor: disable;
	}

</style>