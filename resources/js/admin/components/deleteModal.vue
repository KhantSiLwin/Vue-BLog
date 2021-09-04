<template>
    <div>
        <Modal 
            v-model="getDeleteModalObj.deleteModal"
            :mask-closable="false"
            :closable="false"
            width="360"
            >
            <p slot="header" style="color:#f60;text-align:center">
            <Icon type="md-alert" />
                <span>Delete Confirmation</span>
            </p>
            <div style="text-align:center">
                <p>Are you sure?</p>
            </div>
            <div slot="footer">
                <Button type="default" @click="closeModal">Close</Button>
                <Button type="error" @click="deleteTag"  :disabled="isDeleting" :loading="isDeleting">{{isDeleting ? 'Deleting' : 'Delete Category' }}</Button>
            </div>
        </Modal>
    </div>
</template>

<script>
import {mapGetters} from 'vuex'
export default {
    
    data(){
        return {
            isDeleting :false,
        }
    },

    methods : {

        async deleteTag(){
			
			  this.isDeleting=true
			  const res = await this.callApi('post',this.getDeleteModalObj.deleteUrl,this.getDeleteModalObj.data)
				if(res){
					this.isDeleting=false
					if(res.status==200){
			
					this.s(res.data)
					this.$store.commit('setDeleteModal',true)
					
				}else{
					if(res.status==422){
						if(res.data.errors.id){
							this.e(res.data.errors.id[0])
						}
					}else{
						this.swr()
					}
					this.$store.commit('setDeleteModal',false)
				}
				}
			},
            closeModal(){
                this.$store.commit('setDeleteModal',false)
            }

    },

    computed : {
        ...mapGetters(['getDeleteModalObj'])
    }

}
</script>