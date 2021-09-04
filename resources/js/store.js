import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)

export default new Vuex.Store({
  
    state : {
        deleteModalObj : {

            deleteModal : false,
            deleteUrl : '',
            data : null,
            deletingIndex:-1,
            isDeleted :false
        },
        user:false,
        userPermission:null
    },

    getters:{
        getDeleteModalObj(state){
            return state.deleteModalObj
        },
        getUpdateUser(state){
            return state.user
        },
        getUserPermission(state){
            return state.userPermission
        },
    },

    mutations: {

        setDeleteModal(state,data){

            const deleteModalObj = {

                deleteModal : false,
                deleteUrl : '',
                data : null,
                deletingIndex:-1,
                isDeleted :data
        }
            state.deleteModalObj= deleteModalObj
          
        },
        setDeletingModalObj(state,data){
            state.deleteModalObj = data
        },

        setUpdateUser(state,data){
            state.user = data
        },

        setUpdateUserAdminEdit(state,data){
            state.user.fullName = data.fullName
            state.user.email = data.email
            state.user.role_id = data.role
        },

        setUserPermission(state,data){
            state.userPermission = data
        }

    },

    actions : {

    }
})