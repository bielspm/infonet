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
 *      title="Infonet",
 *      description="",
 * )

 * @OA\Tag(
 *     name="api",
 *     description="Operações relacionadas a usuários"
 * )

 * @OA\PathItem(
 *     path="/api/"
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
    
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Autenticação"},
     *     summary="Autenticação de usuário",
     *     description="Realiza login e retorna o token JWT e nome do usuário",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login", "senha"},
     *             @OA\Property(property="login", type="string", example="usuario@email.com"),
     *             @OA\Property(property="senha", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="nome", type="string", example="João da Silva")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas"
     *     )
     * )
     */
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

    /**
    * @OA\Get(
    *     path="/api/servicos",
    *     tags={"Serviços"},
    *     summary="Listar serviços disponíveis",
     *     description="Retorna a lista de serviços cadastrados",
     *     @OA\Response(
     *         response=200,
     *         description="Serviços retornados com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nome", type="string", example="Transporte de Cargas"),
     *                 @OA\Property(property="situacao", type="string", example="1(ativo) ou 0(falso)")
     *            )
     *         )
     *     )
     * )
     */
    public function listarServicos(){
        return response()->json($this->servicoRepository->obterServicos());
    }

    /**
     * @OA\Get(
     *     path="/api/coordenadas/{endereco}",
     *     tags={"Geolocalização"},
     *     summary="Buscar coordenadas geográficas de um endereço",
     *     description="Consulta latitude e longitude a partir de um endereço. Requer autenticação via Basic Auth.",
     *     security={{"basicAuth":{}}},
     *     @OA\Parameter(
     *         name="endereco",
     *         in="query",
     *         required=true,
     *         description="Endereço completo para geocodificação",
     *         @OA\Schema(type="string", example="Av. Paulista, 1000, São Paulo, SP")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Coordenadas encontradas",
     *         @OA\JsonContent(
     *             @OA\Property(property="latitude", type="number", format="float", example=-23.561684),
     *             @OA\Property(property="longitude", type="number", format="float", example=-46.656139)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Endereço não encontrado"
     *     )
     * )
     */
    public function buscarCoordenadas(Request $request){
        $endereco = $request->query('endereco');
        return $this->geoService->obterCoordenadas($endereco);
    }

    /**
     * @OA\Post(
     *     path="/api/prestadores/buscar",
     *     tags={"Prestadores"},
     *     summary="Buscar prestadores de serviço",
     *     description="Retorna uma lista de prestadores com base nos dados de origem, destino, serviço, filtros e ordenação.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"origem", "destino", "idServico"},
     *             @OA\Property(
     *                 property="origem",
     *                 type="object",
     *                 @OA\Property(property="cidade", type="string"),
     *                 @OA\Property(property="UF", type="string"),
     *                 @OA\Property(property="latitude", type="number", format="float"),
     *                 @OA\Property(property="longitude", type="number", format="float")
     *             ),
     *             @OA\Property(
     *                 property="destino",
     *                 type="object",
     *                 @OA\Property(property="cidade", type="string"),
     *                 @OA\Property(property="UF", type="string"),
     *                 @OA\Property(property="latitude", type="number", format="float"),
     *                 @OA\Property(property="longitude", type="number", format="float")
     *             ),
     *             @OA\Property(property="idServico", type="integer"),
     *             @OA\Property(property="quantidade", type="integer", example=10, description="Máximo 100"),
     *             @OA\Property(
     *                 property="ordenacao",
     *                 type="array",
     *                 @OA\Items(type="string", enum={"valor_total", "distancia_total", "status"})
     *             ),
     *             @OA\Property(
     *                 property="filtros",
     *                 type="object",
     *                 @OA\Property(property="cidade", type="string"),
     *                 @OA\Property(property="UF", type="string"),
     *                 @OA\Property(property="status", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de prestadores encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Prestador")
     *         )
     *     )
     * )
     */
    public function buscarPrestadores(Request $request){
        #TODO
    }
}

/**
 * @OA\Schema(
 *     schema="Prestador",
 *     type="object",
 *     required={"id", "nome", "cidade", "UF", "valor_total", "distancia_total", "status"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nome", type="string", example="João dos Fretes"),
 *     @OA\Property(property="cidade", type="string", example="Campinas"),
 *     @OA\Property(property="UF", type="string", example="SP"),
 *     @OA\Property(property="valor_total", type="number", format="float", example=150.00),
 *     @OA\Property(property="distancia_total", type="number", format="float", example=23.5),
 *     @OA\Property(property="status", type="string", example="ativo")
 * )
 */
class PrestadorResource{}

