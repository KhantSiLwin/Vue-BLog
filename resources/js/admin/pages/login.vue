<template>
    <div>
        <div class="container-fluid">
            <div style="min-height:100vh;" class="d-flex justify-content-center align-items-center">
             <div class="col-12 col-md-5 col-lg-4">
                   <div class="card border-0 shadow-lg">
                    <div class="card-header bg-primary text-white border-0 d-flex align-itmes-center justify-content-center">
                       <img src="images/logo.svg" class="mr-2" style="width: 22px;"/> 
                        <h4 >Vue Blog Admin</h4>
                    </div><hr>
                   <div class="card-body">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <Input name="email" v-model="data.email" @keyup.enter="login" type="text" placeholder="Enter Your Email..." autofocus></Input>
                        </div>
                        <div class="form-group">
                             <label for="password">Password</label>
                            <Input name="password" v-model="data.password" @keyup.enter="login" type="password" placeholder="Enter Your Password..."></Input>
                        </div>
                        <Button type="primary" @click="login" :disabled="isLoggingin" :loading="isLoggingin" class="btn-block mt-4 shadow">Login</Button>
                   </div>
               </div>
             </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                data:{
                    email: '',
                    password: ''
                },
                isLoggingin: false,
            }
        },
        methods : {
            async login(){
                 if(this.data.email.trim()=='') return this.e('Email is Required')
                if(this.data.password.trim()=='') return this.e('Password is Required')
                if(this.data.password<6) return this.e('Password will be at least 6')

                this.isLoggingin=true

                const res = await this.callApi('post','app/admin_login',this.data)
				if(res){
					this.isLoggingin=false
					if(res.status==200){
					 
					window.location ='/'
					// this.s('Logged in successfully!')
					
				}else{
					if(res.status==401){
                        this.i(res.data.msg)
					}else if(res.status==422){
                        for(let i in res.data.errors){
                        this.e(res.data.errors[i][0])
                    }
                    }else{
						this.swr()
					}
					
				}
				}
            }
        }
    }
</script>

<style scoped>

    .ivu-btn{
        height:41px;
    }

</style>