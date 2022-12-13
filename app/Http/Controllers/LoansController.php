<?php

namespace App\Http\Controllers;

use App\Models\loans;
use App\Models\loans_user;
use App\Models\loans_new;
use Illuminate\Http\Request;

class LoansController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        //dd($user);
        $APIController = new APIController();
        $response = $APIController->getModel('AD_User', '', "Name eq '" . $user->name . "'", '', '', '', 'AD_User_OrgAccess');
        //dd($response->records[0]->AD_Org_ID->id);
        $response = $APIController->getModel('AD_Org', '', 'AD_Org_ID eq ' . $response->records[0]->AD_Org_ID->id);
        $orgs =  $response;
        //dd($orgs);
        return view('loans', ['orgs' => $orgs]);
    }
    public function search(Request $request)
    {
        $user = auth()->user();
        $APIController = new APIController();
        $response = $APIController->getModel('AD_User', '', "Name eq '" . $user->name . "'", '', '', '', 'AD_User_OrgAccess');
        $response = $APIController->getModel('AD_Org', '', 'AD_Org_ID eq ' . $response->records[0]->AD_Org_ID->id);
        $orgs =  $response;
        $usuario = loans_user::where('cedula', $request->cedula)->orwhere('nombre', '%' . $request->nombre . '%')->get();
        $usuario_loans = loans_new::where('cedula_user', $request->cedula)->get();
        $usuario_monto = loans_new::select(loans_new::raw("SUM(monto)"))->where('cedula_user', $request->cedula)->get();
        //dd($usuario_loans);
        return view(
            'loans',
            [
                'usuario' => $usuario,
                'usuario_monto' => $usuario_monto,
                'usuario_loans' => $usuario_loans,
                'orgs' => $orgs,
                'cedula' => $request->cedula,
                'nombre' => $request->nombre
            ]
        );
    }

    public function store(Request $request)
    {
        return view('loans');
    }

    public function store_new(Request $request)
    {
        $todo = new loans_new;
        $todo->fechanuevoprestamo   = $request->fechanuevoprestamo;
        $todo->monto                = $request->monto;
        $todo->cuota                = $request->cuota;
        $todo->frecuencia           = $request->frecuencia;
        $todo->filecedula           = $request->filecedula;
        $todo->firmanuevoprestamo   = $request->firmanuevoprestamo;
        $todo->estado =               "Nuevo";
        $todo->cedula_user          = $request->cedula_user;
        //dd($todo);
        $todo->save();
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $user = auth()->user();
        $APIController = new APIController();
        $response = $APIController->getModel('AD_User', '', "Name eq '" . $user->name . "'", '', '', '', 'AD_User_OrgAccess');
        $response = $APIController->getModel('AD_Org', '', 'AD_Org_ID eq ' . $response->records[0]->AD_Org_ID->id);
        $orgs =  $response;
        return view('loans', ['orgs' => $orgs]);
    }


    public function newuser(Request $request)
    {
        $todo = new loans_user;
        $todo->nombre = $request->nombre;
        $todo->cedula = $request->cedula;
        $todo->telefono = $request->telefono;
        $todo->solicitante = $request->solicitante;
        $todo->direccion = $request->direccion;
        $todo->fotocedula = $request->fotocedula;
        $todo->save();
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $user = auth()->user();
        $APIController = new APIController();
        $response = $APIController->getModel('AD_User', '', "Name eq '" . $user->name . "'", '', '', '', 'AD_User_OrgAccess');
        $response = $APIController->getModel('AD_Org', '', 'AD_Org_ID eq ' . $response->records[0]->AD_Org_ID->id);
        $orgs =  $response;
        return view('loans', ['orgs' => $orgs]);
    }

    public function list()
    {
        return view('loans');
    }
}
