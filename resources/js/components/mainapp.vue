<template>

  <div>
    <div v-if="$store.state.user">
      <!--========== ADMIN SIDE MENU one ========-->
      <div class="_1side_menu" >
        <div class="py-3">
          <div class="d-flex justify-content-center align-items-center">
         <img src="images/logo.svg" class="mr-2" style="width: 35px;"/>
          <h4 style="text-align:center;">Vue Blog</h4>
         </div>
        </div>
        <hr style="margin: 5px 18px;">
        <!--~~~~~~~~ MENU CONTENT ~~~~~~~~-->
        <div class="">
         

          <!--~~~ MENU LIST ~~~~~~-->
          <div class="_1side_menu_list my-0">
            <ul class="_1side_menu_list_ul">
              <li v-for="(menuItem,i) in getUserPermission" :key="i" v-if="getUserPermission.length && menuItem.read">
                <router-link  :to="menuItem.name"><a><Icon :type="menuItem.iconName" /> {{menuItem.resourceName}}</a></router-link>
              </li>
                <li><a @click="logout=true" ><a><Icon type="md-lock" />Logout</a></a></li>
          
          
              <!-- <li><router-link to="tags"><a><Icon type="ios-pricetags" /> Tags</a></router-link></li>
              <li><router-link to="category"><a><Icon type="md-git-branch" />Category</a></router-link></li>
              <li><router-link to="role"><a><Icon type="md-build" />Role Management</a></router-link></li>
               <li><router-link to="assignRole"><a><Icon type="md-bookmark" />Assign Role</a></router-link></li>
               <li><router-link to="admin-users"><a><Icon type="md-people" />Admin Users</a></router-link></li>
               -->

            </ul>
          </div>

        </div>
      </div>
      <!--========== ADMIN SIDE MENU ========-->

      <!--========= HEADER ==========-->
      <div class="header">
        <div class="_2menu _box_shadow">
          <div class="">
            <ul class="open_button">
             
           
            </ul>
          </div>
        </div>
      </div>
      <!--========= HEADER ==========-->
    </div>

     <Modal   
             v-model="logout"
            :mask-closable="false"
            :closable="false"
            width="360"
            >
            <p slot="header" style="color:#f60;text-align:center">
            <Icon type="md-alert" />
                <span>Logout Confirmation</span>
            </p>
            <div style="text-align:center">
                <p>Are you sure?</p>
            </div>
            <div slot="footer">
                <Button type="default" @click="logout=false">Close</Button>
               <a class="btn btn-sm logout-btn" href="logout"><Icon type="md-lock" />Logout</a>
            </div>
        </Modal>


    <transition name="fade">
    	<router-view/>
    </transition>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
export default {
  props: ['user','permission'],
  data(){
    return {
      logout:false,
      isLoggedIn : false,
    }
  },
  created(){
   
      this.$store.commit('setUpdateUser',this.user)
      this.$store.commit('setUserPermission',this.permission)

  },
    computed : {
			...mapGetters(['getUserPermission'])
		},
}
</script>

<style>
 .fade-enter-active {
  animation: fade-in .3s;
}
.fade-leave-active {
  animation: fade-in .3s reverse;
}
@keyframes fade-in {
  0% {
    opacity: 0;
  }
 
  100% {
    opacity: 1;
  }
}

.logout-btn{
 background:#c22a03 !important;
  color:white !important;
}

.logout-btn:hover, .logout-btn:focus{
  color:white !important;
  box-shadow:none !important;
}
</style>




