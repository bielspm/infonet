<?php

namespace App\Http\Controllers;


use App\Models\prestador;
use App\Repositorys\ServicoRepository;
use App\Services\GeoService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="L5 OpenApi",
 *      description="L5 Swagger OpenApi description",
 * )

 * @OA\Tag(
 *     name="Usuários",
 *     description="Operações relacionadas a usuários"
 * )

 * @OA\PathItem(
 *     path="/api/usuarios"
 * )
*/
class ApiController extends Controller
{
    public function __construct(
        ServicoRepository $servicoRepository,
        GeoService $geoService)
    {
        $this->servicoRepository = $servicoRepository;
        $this->geoService = $geoService;
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }

    public function respondWithToken($token){

    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login');
    }

    public function dashboard()
    {
        try {
            $user = JWTAuth::setToken(session('jwt_token'))->authenticate();
        } catch (\Exception $e) {
            return redirect()->route('login');
        }

        return view('dashboard', compact('user'));
    }

    public function listarServicos(){
        return response()->json($this->servicoRepository->obterServicos());
    }

    public function buscarCoordenadas(Request $request){
        $endereco = $request->query('endereco');
        return $this->geoService->obterCoordenadas($endereco);
    }

    /**
     * @OA\Get(
     *     path="/api/test",
     *     summary="Test endpoint",
     *     tags={"Test"},
     *     @OA\Response(
     *         response=200,
     *         description="Test successful"
     *     )
     * )
     */
    public function obterPrestadores(Request $request){
        return response()->json(Prestador::all());
    }
}

