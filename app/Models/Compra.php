<?php

namespace App\Models;

use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class Compra extends Model
{
  //  use HasFactory;

    protected $table = "compra";


    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'ruc_empresa'              => ['required', 'string', 'max:255'],
            'empresa'                  => ['required', 'string', 'max:255'],
            'claveAcceso'              => ['required', 'string', 'max:255'],
            'numeroFactura'            => ['required', 'string', 'max:255'],
            'fechaEmision'             => ['required', 'string', 'max:255'],
            'razonSocialProveedor'     => ['required', 'string', 'max:255'],
            'nombreComercialProveedor' => ['required', 'string', 'max:255'],
            'identificacionProveedor'  => ['required', 'string', 'max:255'],
            'direccionMatrizProveedor' => ['required', 'string', 'max:255'],
            'direccionEstabProveedor'  => ['required', 'string', 'max:255'],
            'totalSinImpuestos'        => ['required', 'string', 'max:255'],
            'totalDescuento'           => ['required', 'string', 'max:255'],
            'valorICE'                 => ['required', 'string', 'max:255'],
            'subTotalIva0'             => ['required', 'string', 'max:255'],
            'subTotalIva12'            => ['required', 'string', 'max:255'],
            'iva12'                    => ['required', 'string', 'max:255'],
            'propina'                  => ['required', 'string', 'max:255'],
            'valorTotal'               => ['required', 'string', 'max:255'],
        ]);

        $compra = Compra::create([
            'ruc_empresa'              => $request->ruc_empresa,
            'empresa'                  => $request->empresa,
            'claveAcceso'              => $request->claveAcceso,
            'numeroFactura'            => $request->numeroFactura,
            'fechaEmision'             => $request->fechaEmision,
            'razonSocialProveedor'     => $request->razonSocialProveedor,
            'nombreComercialProveedor' => $request->nombreComercialProveedor,
            'identificacionProveedor'  => $request->identificacionProveedor,
            'direccionMatrizProveedor' => $request->direccionMatrizProveedor,
            'direccionEstabProveedor'  => $request->direccionEstabProveedor,
            'totalSinImpuestos'        => $request->totalSinImpuestos,
            'totalDescuento'           => $request->totalDescuento,
            'valorICE'                 => $request->valorICE,
            'subTotalIva0'             => $request->subTotalIva0,
            'subTotalIva12'            => $request->subTotalIva12,
            'iva12'                    => $request->iva12,
            'propina'                  => $request->propina,
            'valorTotal'               => $request->valorTotal,
        ]);

        event(new Registered($compra));



        return to_route('usuario.compra');
    }


}
