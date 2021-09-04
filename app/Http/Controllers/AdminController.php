<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Validated;

class AdminController extends Controller
{
    
    public function index(Request $request){

        if(!Auth::check() && $request->path() != 'login'){
            return redirect('login');
        }

        if(!Auth::check() && $request->path() == 'login'){
            return view('welcome');
        }

        // $user = Auth::user();
        // if($user->role->isAdmin ==0){
        //     return redirect('/login');
        // }
        // if($request->path() == 'login'){
        //     return redirect('/');
        // }

        // return $this->checkForPermission($user,$request);

        return view('welcome');

    }

    public function checkForPermission($user,$request){

        $permission = json_decode($user->role->permission);

        $hasPermission= false;
        foreach ($permission as $p) {
            if($p->name==$request->path()){
                if($p->read){
                    $hasPermission=true;
                }
            }
        }

        if($hasPermission) return view('welcome');
        return view('notfound');

    }

    public function logout(){
        Auth::logout();
        return redirect('/login');
    }

    public function addTag(Request $request){
       
        $this->validate($request,[
            'tagName' => 'required|min:2'
        ]);

       return Tag::create([
           'tagName' => $request->tagName
       ]);
       
    
    }

    public function editTag(Request $request){
      
        $this->validate($request,[
            'tagName' => 'required|min:2',
            'id' => 'required'
        ]);

       return Tag::where('id',$request->id)->update([
            'tagName' => $request->tagName
       ]);
       
    
    }

    public function deleteTag(Request $request){
      
        $this->validate($request,[
            'id' => 'required'
        ]);

       if(Tag::where('id',$request->id)->delete()){
        return 'Tag has been deleted successfully!';
       }
    
    }

    public function getTags(){
       
        return Tag::orderBy('id','desc')->get();
       
     }

     public function upload(Request $request){
       
        $this->validate($request,[
            'file' => 'required|mimes:png,jpg,jpeg'
        ]);

         $picName = time().'.'.$request->file->getClientOriginalName();
         $request->file->move(public_path('uploads'),$picName);
        return "uploads/".$picName;
     }

     public function deleteImage(Request $request){
      
        $fileName = $request->imageName;
       
       if( $this->deleteFileFromServer($fileName)){
        return "done";
       }
       return 'File not exits';
     }

     public function deleteFileFromServer($fileName){

        $filePath = public_path('/').$fileName;

        if(file_exists($filePath)){
            unlink($filePath);
            return true;
        }
       return false;
     }

     public function addCategory(Request $request){
        $this->validate($request,[
            'categoryName' => 'required|min:2',
            'iconImage' => 'required'
        ]);

       return Category::create([
           'categoryName' => $request->categoryName,
           'iconImage' => $request->iconImage
       ]);
       
    
    }

    public function editCategory(Request $request){
        $this->validate($request,[
            'id'=>'required',
            'categoryName' => 'required|min:2',
            'iconImage' => 'required'
        ]);

       return Category::where('id',$request->id)->update([
           'categoryName' => $request->categoryName,
           'iconImage' => $request->iconImage
       ]);
       
    
    }

    public function deleteCategory(Request $request){
      
        $this->validate($request,[
            'id' => 'required'
        ]);

       $category = Category::where('id',$request->id)->first();
       $this->deleteFileFromServer($category->iconImage);
       if($category->delete()){
            return 'Category has been deleted successfully!';
       }
        
       
    
    }

    public function getCategories(){
       
        return Category::orderBy('id','desc')->get();
       
     }

     
     public function addAdminUser(Request $request){
      
        $this->validate($request,[
            'fullName' => 'required|min:2',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);
        
       return User::create([
           'fullName' => $request->fullName,
           'email' => $request->email,
           'password' => bcrypt($request->password),
           'role_id' => $request->role
       ]);
       
    
    }

    public function editAdminUser(Request $request){

        if($request->password){
            $this->validate($request,[
                'password' => 'min:6',
            ]);
        }

         $this->validate($request,[
            'fullName' => 'required|min:2',
            'email' => 'required|email|unique:users,id,$request->id',
            'role' => 'required',
        ]);

        $data=[
            'fullName' => $request->fullName,
            'email' => $request->email,
            'role_id' => $request->role
        ];
        
        if($request->password){
            $password =bcrypt($request->password);
            $data['password'] = $password; 
        }

        return User::where('id',$request->id)->update($data);
       
    
    }

    public function deleteAdminUser(Request $request){
      
        $this->validate($request,[
            'id' => 'required'
        ]);

       if(User::where('id',$request->id)->delete()){
        return 'User has been deleted successfully!';
       }
    
    }

    public function getAdminUser(){
       
        return User::orderBy('id','desc')->get();
       
     }


     public function adminLogin(Request $request){

        $this->validate($request,[
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if(Auth::attempt(['email'=> $request->email, 'password'=>$request->password])){
           
            $user = Auth::user();
            // return response()->json([
            //             'msg' => $user->role->roleName
            //         ], 401);
            if($user->role->isAdmin == 0){
                Auth::logout();
                return response()->json([
                    'msg' => 'Login Invalid.'
                ], 401);
            }
           
            return response()->json([
                'msg' => 'You are logged in.'
            ]);
        }else{
            return response()->json([
                'msg' => 'Login Invalid.'
            ], 401);
        }

     }

     public function addRole(Request $request){
        $this->validate($request,[
            'roleName' => 'required|min:2',
            'permission'=> 'required'
        ]);

       return Role::create([
           'roleName' => $request->roleName,
           'permission'=> $request->permission
       ]);
       
    
    }

    public function editRole(Request $request){
      
        $this->validate($request,[
            'roleName' => 'required|min:2',
            'id' => 'required'
        ]);

       return Role::where('id',$request->id)->update([
            'roleName' => $request->roleName
       ]);
       
    
    }

    public function deleteRole(Request $request){
      
        $this->validate($request,[
            'id' => 'required'
        ]);

       if(Role::where('id',$request->id)->delete()){
        return 'Role has been deleted successfully!';
       }
    
    }

    public function getRoles(){
       
        return Role::orderBy('id','desc')->get();
       
     }


     public function assignRoles(Request $request){
       
        $this->validate($request,[
            'id' => 'required',
            'permission' => 'required'
        ]);

        return Role::where('id',$request->id)->update([
            "permission"=>$request->permission
        ]);
       
     }
}
