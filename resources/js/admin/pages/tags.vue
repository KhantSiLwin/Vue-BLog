<template>
		<div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<div class="d-flex justify-content-between align-items-center mb-2">
					<p class="_title0 m-0"><Icon type="md-pricetags" />  Tags</p>
					<button v-if="isWritePermitted" @click="addModal=true" class="btn btn-outline-primary btn-sm"><Icon type="md-add-circle" />  Add Tag</button>
				</div>
					<div class="_overflow _table_div">
					<table class="table table-bordered table-hover shadow-sm table-striped">
								<!-- TABLE TITLE -->
							<thead class="bg-primary text-white">
							<tr class="text-center">
								<th>Id</th>
								<th>Tag Name</th>
								<th>Created at</th>
								<th>Action</th>
							</tr>
							</thead>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tbody v-if="tags[0]">
							<tr class="text-center" v-for="(tag,i) in tags" :key="i">
								<td>{{id=i+1}}</td>
								<td>{{tag.tagName}}</td>
								<td>{{tag.created_at}}</td>
								<td>
									<button v-if="isUpdatePermitted" class="btn btn-sm btn-outline-info" @click="showEdit(tag,i)" type="button"><Icon type="md-create" /></button>
									<button v-if="isDeletePermitted" class="btn btn-sm btn-outline-danger"  @click="showDelete(tag,i)" type="button"><Icon type="ios-trash" /></button>

								</td>
							</tr>
							</tbody>
								<!-- ITEMS -->

							<tbody v-else>
							<tr class="">
								<td v-if="apiloading" colspan="4" class="text-center"><img src="images/loading.gif" style="width:35px;height:35px;margin:auto;" alt=""></td>
								<td  v-else colspan="4" class="text-center">There is no tags.. </td>
							</tr>
							</tbody>

						</table>
					</div>

					<!-- tag adding  model -->
					<Modal 
						v-model="addModal"
						title="Add Tag"
						:mask-closable="false"
						:closable="false"
						width="360px"
						>
						  <!-- <Input v-model="data.tagName" @keyup.enter="addTag" placeholder="Enter Tag Name..." style="width: 300px" /> -->
							<input type="text" class="form-control" v-model="data.tagName" @keyup.enter="addTag" style="width: 300px" placeholder="Enter Tag Name..." autofocus>
						<div slot="footer">
							<Button type="default" @click="closeModalClearData">Close</Button>
							<Button type="primary" @click="addTag" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding' : 'Add Tag' }}</Button>
						</div>
					</Modal>

					<Modal 
						v-model="editModal"
						title="Edit Tag"
						:mask-closable="false"
						:closable="false"
						width="360px"
						>
						  <!-- <Input v-model="editData.tagName" placeholder="Enter Tag Name..." style="width: 300px" /> -->
						<input type="text" class="form-control" v-model="editData.tagName" @keyup.enter="editTag" style="width: 300px" placeholder="Enter Tag Name..."/>

						<div slot="footer">
							<Button type="default" @click="editModal=false">Close</Button>
							<Button type="primary" @click="editTag" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Editing' : 'Edit Tag' }}</Button>
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
					tagName: ''
				},
				apiloading:true,
				addModal : false,
				isAdding : false,
				editModal : false,
				isEditing : false,
				tags:[],
				editData:{
					tagName: ''
				},
				index: -1,
				deletingIndex:-1
			}
		},

		methods: {
		async addTag(){
				if(this.data.tagName.trim()=='') return this.e('Tag name is Reqired')
				this.isAdding=true
				const res = await this.callApi('post','app/create_tag',this.data)
				if(res){
					this.isAdding=false
					if(res.status==201){
					this.tags.unshift(res.data)
					this.s('Tag has been added successfully!')
					this.addModal=false
					this.data.tagName=''
				}else{
					if(res.status==422){
						if(res.data.errors.tagName){
							this.e(res.data.errors.tagName[0])
						}
					}else{
						this.swr()
					}
					
				}
				}
			},

			closeModalClearData(){

				this.addModal=false
				this.data.tagName=''

			},


			async editTag(){
				if(this.editData.tagName.trim()=='') return this.e('Tag name is Reqired')
			    this.isEditing=true
				const res = await this.callApi('post','app/edit_tag',this.editData)
				if(res){
					this.isEditing=false
					if(res.status==200){
					 
					this.tags[this.index].tagName=this.editData.tagName
					this.s('Tag has been edited successfully!')
					this.editModal=false
					
				}else{
					if(res.status==422){
						if(res.data.errors.tagName){
							this.e(res.data.errors.tagName[0])
						}
					}else{
						this.swr()
					}
					
				}
				}
			},
			showEdit(tag,index){
				let obj = {
					id : tag.id,
					tagName:tag.tagName
				}
				this.editData = obj
				this.editModal =true
				this.index = index
			},
			
			
			showDelete(tag,index){
				const deleteModalObj = {

					deleteModal : true,
					deleteUrl : 'app/delete_tag',
					data : tag,
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
			// console.log(this.isDeletePermitted)
			const res = await this.callApi('get','app/get_tags')
				if(res.status=200){
					this.apiloading=false
					this.tags =res.data
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
					this.tags.splice(this.deletingIndex,1)
				}
			}
		}
        
		

    }
</script>

<style lang="scss" scoped>

</style>