@extends('layouts::master')

@section('title', 'Instaldor ABM Global')

@section('sidebar')
    @parent

    <p>This is appended to the master sidebar.</p>
@endsection

@section('content')
    <div class="panel-heading">Instalacion ABM Generico</div>

    <div class="panel-body">
        <div class="help-block">
            En esta sección usted será capaz de crear un ABM Genérico donde se incluirán las siguientes vistas/modelos/controladores con sus validaciones:

            <ul>
                <li>Nombre</li>
                <li>Descripcion</li>
                <li>Imagen</li>
                <li>Estado</li>
            </ul>

            Para comenzar, escriba el nombre del ABM que desee crear.
        </div>

        <div class="col-lg-7">
            <form action="/install/crear-abm-generico" method="POST">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('codigo') ? ' has-error' : '' }}">
                    <label class="col-md-4 control-label">Nombre del ABM</label>

                    <div class="col-md-6">
                        <input type="text" class="form-control" name="nombre_abm" value="{{ old('nombre_abm') }}" placeholder="Ingrese el nombre de su ABM">

                        @if ($errors->has('nombre_abm'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre_abm') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <br>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            Crear
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection