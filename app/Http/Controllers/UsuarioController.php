<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str; //Extencion para importar imagen
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Datatable;
//use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\DataTables;

class UsuarioController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
       if ($request->ajax()) {

        $usuarios = User::select('users.*');
       //$usuarios = User::all();

        return DataTables::of($usuarios)
           // anadir nueva columna botones
            ->addColumn('actions', function($usuarios){
              return  '<a href="'.$usuarios->id.'" class="btn btn-primary btn-sm">Editar</a>';
            })
           // 
            ->addColumn('foto', function($usuarios){
               $imagen='imagenes/usuarios/'.$usuarios->id.'.jpg';
                if (!file_exists($imagen)) {
                 $imagen = "imagenes/usuarios/150x150.png";
                }
               $url=asset($imagen.'?'.time());
              return  '<img width="50" height="30" src="'.$url.'"/>';
            })
            ->rawColumns(['actions','foto']) // incorporar columnas
            ->make(true); // convertir a codigo
        }

    return view('usuarios.index');

    }

    public function DatosServerSideActivo(Request $request){
        if ($request->ajax()) {
             $user=User::select('users.*')->with('roles')->where('users.estado','=',1);
             //$user=$user->where('estado',1);
            // $user=User::all()->where('estado',1);
             return DataTables::of($user)
                // anadir nueva columna botones
                 ->addColumn('actions', function($user){
                    // $url= route('rol.permisos',$roles->id);
                    // $url2= route('rol.destroy', $roles->id);
                     $btn= '<div class="btn-group btn-group-sm">'
                     .'<a class="btn btn-dark" rel="tooltip" data-placement="top"  onclick="Modificar('.$user->id.')" ><i class="far fa-edit"></i></a>'
                     .'<a class="btn btn-dark" rel="tooltip" data-placement="top"  onclick="Eliminar('.$user->id.')"><i class="far fa-trash-alt"></i></a>
                     </div>';
                   return  $btn;
                 })
                // 
                ->addColumn('foto', function($user){
                    $imagen='imagenes/usuarios/'.$user->id.'.jpg';
                     if (!file_exists($imagen)) {
                      $imagen = "imagenes/usuarios/150x150.png";
                     }
                    $url=asset($imagen.'?'.time());
                   return  '<img width="50" height="30" src="'.$url.'"/>';
                })
                ->addColumn('rol_uso' , function($user){
                    if (isset($user->roles['0']->name)){
                    return $user->roles['0']->name;
                    }else{
                       return 'no tiene rol';
                    }
                })
                 ->rawColumns(['actions','foto','rol_uso']) // incorporar columnas
                 ->make(true); // convertir a codigo
         }
    }

    public function DatosServerSideInactivo(Request $request){
        if ($request->ajax()) {
            $user=User::select('users.*')->with('roles')->where('users.estado','=',0);
           // $user=$user->where('estado',0);
            // $user=User::all()->where('estado',0);
             return DataTables::of($user)
                // anadir nueva columna botones
                 ->addColumn('actions', function($user){
                     //$url= route('rol.permisos',$roles->id);
                     $btn= '<div class="btn-group btn-group-sm">'
                     .'<a class="btn btn-secondary" rel="tooltip" data-placement="top" title="Restaurar" onclick="Restaurar('.$user->id.')"><i class="fas fa-arrow-alt-circle-up"></i></a>
                     </div>';
                   return  $btn;
                 })
                //
                ->addColumn('foto', function($user){
                    $imagen='imagenes/usuarios/'.$user->id.'.jpg';
                     if (!file_exists($imagen)) {
                      $imagen = "imagenes/usuarios/150x150.png";
                     }
                    $url=asset($imagen.'?'.time());
                   return  '<img width="50" height="30" src="'.$url.'"/>';
                })
                ->addColumn('rol_uso' , function($user){
                    if (isset($user->roles['0']->name)){
                    return $user->roles['0']->name;
                    }else{
                       return 'no tiene rol';
                    }
                })
                 ->rawColumns(['actions','foto','rol_uso']) // incorporar columnas
                 ->make(true); // convertir a codigo
        }
    }

     
    public function perfil()
    {
        return view('usuarios/perfil');
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:2','confirmed'],
            'edad' => ['required', 'integer'],
            'telefono' => ['required', 'integer'],
            'apellidos' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'img_perfil' => 'image|mimes:jpg,jpeg|max:2048|min:8'
        ]);

        $usuario=User::create([
            'name'=> $request->nombre,
            'email' => $request->email,
            'apellidos'=> $request->apellidos,
            'edad'=> $request->edad,
            'direccion'=> $request->direccion,
            'telefono'=> $request->telefono,
            'password' => Hash::make($request->password),
        ]);
        if ($request->hasFile("img_perfil")) {//existe un campo de tipo file?
            $imagen = $request->file("img_perfil"); //almacenar imagen en variable
            $nombreimagen=Str::slug($usuario->id).".".$imagen->guessExtension();//insertar parametro del nombre de imagen
            $ruta = public_path("imagenes/usuarios/");//guardar en esa ruta
            $imagen->move($ruta,$nombreimagen); //mover la imagen es esa ruta y con ese nombre
            //copy($imagen->getRealPath(),$ruta.$nombreimagen); copiar imagen un una ruta
        }

        return back()->with('success', 'Información almacenada con éxito');
    }

    public function show($id)
    {
        //
    }

   
    public function edit($id)
    {
        //
    }

    
    public function update(Request $request,$id)
    {
        $user=User::findOrFail($id);
        $user->removeRole($request->id_rol_antiguo);
        $user->assignRole($request->id_rol_nuevo);
    }

    public function buscarPoUsuario($id){
        $user=User::findOrFail($id);
        $id_rol_user = User::with('roles')->where('id',$id)->first();
        $id_rol_user = $id_rol_user->roles['0']->id;
        $role = Role::all();
        $res['datos']=$user;
        $res['roles']=$role;
        $res['id_rol_user']=$id_rol_user;
        echo json_encode($res);
    }

    public function restore($id)
    {
        $user = User::findOrFail($id);
        $user->estado = 1;
        $user->update();
    }

    public function destroy($id)
    { 
        $user = User::findOrFail($id);
        $user->estado = 0;
        $user->update();
    }
}
