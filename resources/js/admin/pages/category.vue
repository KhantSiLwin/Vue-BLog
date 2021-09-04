<template>
		<div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
				<div class="d-flex justify-content-between align-items-center mb-2">
					<p class="_title0 m-0"><Icon type="ios-git-branch" />  Categories</p>
					<button v-if="isWritePermitted" @click="addModal=true" class="btn btn-outline-primary btn-sm"><Icon type="md-add-circle" />  Add Category</button>
				</div>

					<div class="_overflow _table_div">
						<table class="table table-bordered table-hover shadow-sm table-striped">
								<!-- TABLE TITLE -->
							<thead class="bg-primary text-white">
								<tr class="text-center">
								<th>Id</th>
								<th>Icon Image</th>
								<th>Category Name</th>
								<th>Created at</th>
								<th>Action</th>
							</tr>
							</thead>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tbody v-if="categories[0]">
							
							<tr class="text-center" v-for="(category,i) in categories" :key="i">
								<td>{{id=i+1}}</td>
								<td><img :src="category.iconImage" class="shadow-lg m-auto" style="width:30px;height:30px;" alt=""></td>
								<td>{{category.categoryName}}</td>
								<td>{{category.created_at}}</td>
								<td>
									<button v-if="isUpdatePermitted" class="btn btn-sm btn-outline-info" @click="showEdit(category,i)" type="button"><Icon type="md-create" /></button>
									<button v-if="isDeletePermitted" class="btn btn-sm btn-outline-danger"  @click="showDelete(category,i)" type="button"><Icon type="ios-trash" /></button>

								</td>
							</tr>
							</tbody>
						
							<tbody v-else>
							<tr class="">
								<td v-if="apiloading" colspan="5" class="text-center"><img src="images/loading.gif" style="width:35px;height:35px;margin:auto;" alt=""></td>
								<td  v-else colspan="5" class="text-center">There is no categories.. </td>
							</tr>
							</tbody>
								<!-- ITEMS -->



						</table>
					</div>

					<!-- category adding  model -->
					<Modal 
						v-model="addModal"
						title="Add Category"
						:mask-closable="false"
						:closable="false"
						width="360px"
						>
					
							<input type="text" class="form-control" v-model="data.categoryName" @keyup.enter="addCategory" style="width: 300px" placeholder="Enter Category Name...">
							<div class="space my-2"></div>
							 <Upload
							 	v-if="!isHasIconImage"
								ref="uploads"
								type="drag"
								:headers="{'x-csrf-token':token,'X-Requested-With':'XMLHttpRequest'}"
								:format="['jpg','jpeg','png']"
								:on-success="handleSuccess"
								:on-error="handleError"
								:on-format-error="handleFormatError"
								:max-size="2048"
								
								:on-exceeded-size="handleMaxSize"
								action="app/upload"
								>
								<div style="padding: 20px 0">
									<Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
									<p>Click or drag files here to upload</p>
								</div>
							</Upload>

							<div v-else class="w-100 d-flex justify-content-center align-items-center">
								<div class="demo-upload-list" v-if="data.iconImage">
								
									<img :src="data.iconImage">
									<div class="demo-upload-list-cover">
										<Icon type="ios-trash-outline" @click="deleteImage"></Icon>
									</div>
								
							</div>
							</div>

						<div slot="footer">
							<Button type="default" @click="isImageExist">Close</Button>
							<Button type="primary" @click="addCategory" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding' : 'Add Category' }}</Button>
						</div>
					</Modal>

					<Modal 
						v-model="editModal"
						title="Edit Category"
						:mask-closable="false"
						:closable="false"
						width="360px"
						>
					
							<input type="text" class="form-control" v-model="editData.categoryName" @keyup.enter="editCategory" style="width: 300px" placeholder="Enter Category Name...">
							<div class="space my-2"></div>
							 <Upload
							 	v-if="!isHasEditIconImage"
								ref="uploads"
								type="drag"
								:headers="{'x-csrf-token':token,'X-Requested-With':'XMLHttpRequest'}"
								:format="['jpg','jpeg','png']"
								:on-success="edithandleSuccess"
								:on-error="edithandleError"
								:on-format-error="edithandleFormatError"
								:max-size="2048"
								
								:on-exceeded-size="edithandleMaxSize"
								action="app/upload"
								>
								<div style="padding: 20px 0">
									<Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
									<p>Click or drag files here to upload</p>
								</div>
							</Upload>

							<div v-else class="w-100 d-flex justify-content-center align-items-center">
								<div class="demo-upload-list" >
								
									<img :src="editData.iconImage">
									<div class="demo-upload-list-cover">
										<Icon type="ios-trash-outline" @click="deleteEditImage"></Icon>
									</div>
								
							</div>
							</div>

						
						<div slot="footer">
							<Button type="default" @click="isEditImageExist">Close</Button>
							<Button type="primary" @click="editCategory" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Editing' : 'Edit Category' }}</Button>
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
					iconImage:'',
					categoryName: ''

				},
				apiloading:true,
				addModal : false,
				isAdding : false,
				editModal : false,
				isEditing : false,
				isHasIconImage:false,
				isHasEditIconImage:false,
				categories:[],
				editData:{
					iconImage:'',
					categoryName: ''
				},
				index: -1,
				deletingIndex:-1,
				token:''
			}
		},

		methods: {
		async addCategory(){
				if(this.data.categoryName.trim()=='') return this.e('Category name is Reqired')
				if(this.data.iconImage.trim()=='') return this.e('Icon image name is Reqired')
				this.data.iconImage = this.data.iconImage
				this.isAdding=true
				const res = await this.callApi('post','app/create_category',this.data)
				if(res){
					this.isAdding=false
					if(res.status==201){
					this.categories.unshift(res.data)
					this.s('Category has been added successfully!')
					this.addModal=false
					this.data.categoryName=''
					this.data.iconImage=''
					this.isHasIconImage=false
					// this.$refs.uploads.clearFiles();
				}else{
					if(res.status==422){
						if(res.data.errors.categoryName){
							this.e(res.data.errors.categoryName[0])
						}
						if(res.data.errors.iconImage){
							this.e(res.data.errors.iconImage[0])
						}
					}else{
						this.swr()
					}
					
				}
				}
			},

			async editCategory(){
				if(this.editData.categoryName.trim()=='') return this.e('Category name is Required')
				if(this.editData.iconImage.trim()=='') return this.e('Icon image is Required')
			
				this.isEditing=true
				const res = await this.callApi('post','app/edit_category',this.editData)
				if(res){
					this.isEditing=false
					if(res.status==200){
					this.categories[this.index].categoryName=this.editData.categoryName
					this.categories[this.index].iconImage=this.editData.iconImage
					this.s('Category has been added successfully!')
					this.editModal=false
					this.editData.categoryName=''
					this.editData.iconImage=''
				}else{
					if(res.status==422){
						if(res.editData.errors.categoryName){
							this.e(res.editData.errors.categoryName[0])
						}
						if(res.editData.errors.iconImage){
							this.e(res.editData.errors.iconImage[0])
						}
					}else{
						this.swr()
					}
					
				}
				}
			},

			showEdit(category,index){
				let obj = {
					id : category.id,
					categoryName:category.categoryName,
					iconImage:category.iconImage
				}
				this.editData = obj
				this.isHasEditIconImage=true
				this.editModal =true
				this.index = index
			},
			
			
			showDelete(category,index){

				const deleteModalObj = {

					deleteModal : true,
					deleteUrl : 'app/delete_category',
					data : category,
					isDeleted :false
        	}
				this.deletingIndex=index
				this.$store.commit('setDeletingModalObj',deleteModalObj)
			},

			
            handleSuccess (res, file) {
                this.data.iconImage = res
				this.isHasIconImage =true
            },
			handleError (res,file) {
                this.e(file.errors.file[0])
            },
            handleFormatError (file) {
                this.$Notice.warning({
                    title: 'The file format is incorrect',
                    desc: 'File format of ' + file.name + ' is incorrect, please select jpg or png.'
                });
            },
            handleMaxSize (file) {
                this.$Notice.warning({
                    title: 'Exceeding file size limit',
                    desc: 'File  ' + file.name + ' is too large, no more than 2M.'
                });
            },

			edithandleSuccess (res, file) {
                this.editData.iconImage = res
				this.isHasEditIconImage =true
            },
			edithandleError (res,file) {
                this.e(file.errors.file[0])
            },
            edithandleFormatError (file) {
                this.$Notice.warning({
                    title: 'The file format is incorrect',
                    desc: 'File format of ' + file.name + ' is incorrect, please select jpg or png.'
                });
            },
            edithandleMaxSize (file) {
                this.$Notice.warning({
                    title: 'Exceeding file size limit',
                    desc: 'File  ' + file.name + ' is too large, no more than 2M.'
                });
            },

			async deleteImage(){
				let image =this.data.iconImage
				this.data.iconImage=''
				this.isHasIconImage=false
				this.$refs.uploads.clearFiles();
				const res = await this.callApi('post','app/delete_image',{imageName:image})
				if(res.status==200){
					return true;
				}
			},

            
			isImageExist(){
				if(this.data.iconImage){
					this.deleteImage()
				}
				this.data.categoryName=''
				this.addModal=false
				
			},

			async deleteEditImage(){
				let image =this.editData.iconImage
				this.editData.iconImage=''
				this.isHasEditIconImage=false
				const res = await this.callApi('post','app/delete_image',{imageName:image})
				if(res.status==200){
					return true;
				}
			},

			isEditImageExist(){
				if(this.editData.iconImage){
					this.editModal=false
				}else{
					if(this.editData.categoryName.trim()=='') return this.e('Category name is Required')
					if(this.editData.iconImage.trim()=='') return this.e('Icon image is Required')
				}
				
				
			}

			
			
		},

		async created(){
			this.token = window.laravel.csrfToken
			const res = await this.callApi('get','app/get_categories')
				if(res.status==200){
					this.apiloading=false
					this.categories =res.data
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
					this.categories.splice(this.deletingIndex,1)
				}
			}
		}
        
		

    }
</script>

<style scoped>


    .demo-upload-list{
        display: inline-block;
        width: 120px;
        height: 120px;
        text-align: center;
        line-height: 60px;
        border: 1px solid transparent;
        border-radius: 4px;
        overflow: hidden;
        background: #fff;
        position: relative;
        box-shadow: 0 1px 1px rgba(0,0,0,.2);
        margin-right: 4px;
    }
    .demo-upload-list img{
        width: 100%;
        height: 100%;
    }
    .demo-upload-list-cover{
        display: none;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,.6);
    }
    .demo-upload-list:hover .demo-upload-list-cover{
        display: flex;
		justify-content: center;
		align-items: center;
    }
    .demo-upload-list-cover i{
        color: #fff;
        font-size: 20px;
        cursor: pointer;
        margin: 0 2px;
    }
</style>
