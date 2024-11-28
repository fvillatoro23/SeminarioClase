<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use App\Models\Alumno;
use App\Http\Controllers\AlumnoController;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Tests\TestCase;

class AlumnoControllerUnitTest extends TestCase
{
    //Prueba de ingresar vacios y que se genere excepcion
    public function test_probar_validacion_falla_para_crear_alumnos()
    {
        // Crear un mock de la solicitud
        $controller = new AlumnoController();
        $request = Request::create('/alumnos', 'POST', [
            'name' => '',
            'lastname' => '',
            'email' => '',
            'age' => '',
        ]);
        //Se espera tener un error
        $this->expectException(ValidationException::class);

        // Ejecutar el controlador
        $controller->store($request);
    }


    //Prueba de insercion correcta
    public function test_probar_validacion_correcta_para_crear_alumnos()
    {
        $controller = new AlumnoController();
        $request = Request::create('/alumnos', 'POST', [
            'name' => 'Diego',
            'lastname' => 'Alvarado',
            'email' => 'daaa@email.com',
            'age' => '19',
        ]);
        //Se espera que no haya error, accion exitosa
        //$this->expectException(ValidationException::class);
        $response = $controller->store($request);
        $this->assertTrue($response->isRedirect(route('alumnos.index')));
    }


    //////////////////////NUEVOS METODOS/////////////////////
    public function test_probar_edad_alumno_es_numerica()
    {
        $controller = new AlumnoController();
        $request = Request::create('/alumnos', 'POST', [
            'name' => 'Hola',
            'lastname' => 'Pérez',
            'email' => 'hperez@email.com',
            'age' => 22,
        ]);

        $controller->store($request);
        $alumnoCreado = Alumno::where('email', 'hperez@email.com')->first();
        $this->assertIsNumeric($alumnoCreado->edad);
    }

    public function test_probar_nombre_actualizado_es_exacto()
    {
        $alumno = Alumno::factory()->create([
            'nombre' => 'Jose',
            'apellido' => 'Mendez',
            'email' => 'jose.mendez@email.com',
            'edad' => 20,
        ]);

        $controller = new AlumnoController();
        $request = Request::create("/alumnos/{$alumno->id}", 'PUT', [
            'nombre' => 'Jose',
            'apellido' => 'Martinez',
            'email' => 'jose.martinez@email.com',
            'edad' => 20,
        ]);

        $controller->update($request, $alumno->id);

        $alumnoActualizado = Alumno::find($alumno->id);
        $this->assertSame('Jose', $alumnoActualizado->nombre);
    }

    public function test_probar_alumno_incorrecto_no_existe()
    {
        $response = $this->post('/alumnos', [
            'name' => '',
            'lastname' => 'Lopez',
            'email' => 'pedro.lopez@email.com',
            'age' => 30,
        ]);

        $this->assertFalse(Alumno::where('email', 'pedro.lopez@email.com')->exists());
    }

    public function test_probar_edad_alumno_guardada_correctamente()
    {
        $alumno = Alumno::factory()->create([
            'nombre' => 'Ana',
            'apellido' => 'Gomez',
            'email' => 'ana.gomez@email.com',
            'edad' => 25,
        ]);

        $this->assertEquals('ana.gomez@email.com', $alumno->email);
    }
}
