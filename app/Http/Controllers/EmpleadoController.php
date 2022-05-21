<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $datos['empleados']=Empleado::paginate(1);
        return view('empleado.index',$datos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('empleado.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)//insersion
    {
        //Mensajes de validacion

        $campos = [
            'Nombre'=>'required|string|max:100',
            'Apellido'=>'required|string|max:100',
            'Correo'=>'required|email',
            'Foto'=>'required|max:10000|mimes:jpeg,png,jpg',
        ];
        $mensaje =[
            'required'=>'El :attribute es requerido',
            'Foto.required'=>'La foto es requerida'
        ];

        $this->validate($request, $campos, $mensaje);

        //$datosEmpleado = request()->all();
        $datosEmpleado = request()->except('_token');

        if($request->hasFile('Foto')){//si existe el archivo...
            $datosEmpleado['Foto']=$request->file('Foto')->store('uploads', 'public');//guarda la foto
        }


        Empleado::insert($datosEmpleado);
        //return response()->json($datosEmpleado);
        return redirect('empleado')->with('mensaje','Empleado agregado');//redirecciona al index con un msj
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function show(Empleado $empleado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $empleado = Empleado::findOrFail($id);//busca un registro con el id q pasamos x param
        return view('empleado.edit', compact('empleado'));//pasando datos a la vista
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Mensajes de validacion
        $campos = [
            'Nombre'=>'required|string|max:100',
            'Apellido'=>'required|string|max:100',
            'Correo'=>'required|email',
        ];
        $mensaje =[
            'required'=>'El :attribute es requerido',
        ];

        if($request->hasFile('Foto')){//si existe el archivo...
            $campos = ['Foto'=>'required|max:10000|mimes:jpeg,png,jpg'];
            $mensaje =['Foto.required'=>'La foto es requerida'];
        }
        $this->validate($request, $campos, $mensaje);


        $datosEmpleado = request()->except(['_token','_method']);

        if($request->hasFile('Foto')){//si existe el archivo...
            $empleado = Empleado::findOrFail($id);//recupera informacion
            Storage::delete('public/'.$empleado->Foto);//borra foto
            $datosEmpleado['Foto']=$request->file('Foto')->store('uploads', 'public');// la foto
        }

        Empleado::where('id','=', $id)->update($datosEmpleado);//busca los datos segun el id y actualiza

        $empleado = Empleado::findOrFail($id);//recupera informacion
        //return view('empleado.edit', compact('empleado'));
        return redirect('empleado')->with('mensaje','Empleado modificado');//redirecciona al index con msj
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $empleado = Empleado::findOrFail($id);//recupera informacion
        if(Storage::delete('public/'.$empleado->Foto)){//borra la foto de la carpeta
            Empleado::destroy($id);
        }

        
        return redirect('empleado')->with('mensaje','Empleado borrado');//redirecciona al index con msj
    }
}
