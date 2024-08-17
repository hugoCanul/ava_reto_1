use Illuminate\Support\Facades\Route;
use App\Http\Controller\Api\TaskController;


Route::apiResource('tasks',TaskController::class);