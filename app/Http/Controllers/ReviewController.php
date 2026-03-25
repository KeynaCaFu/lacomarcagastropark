<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\ReviewData;

class ReviewController extends Controller
{
    protected $reviewData;

    public function __construct(ReviewData $reviewData)
    {
        $this->reviewData = $reviewData;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $local = $user->locals()->first();

        $reviews = collect();

        if ($local) {
            $reviews = $this->reviewData->getByLocal($local->local_id);
        }

        return view('reviews.index', compact('reviews', 'local'));
    }

    public function respond(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string|max:1000'
        ], [
            'response.required' => 'La respuesta es obligatoria.',
            'response.max' => 'La respuesta no puede tener más de 1000 caracteres.'
        ]);

        $user = $request->user();
        $local = $user->locals()->first();

        if (!$local) {
            return redirect()->route('reviews.index')
                ->with('error', 'No tienes un local asignado.');
        }

        $ok = $this->reviewData->respond(
            $id,
            $local->local_id,
            $request->response
        );

        if (!$ok) {
            return redirect()->route('reviews.index')
                ->with('error', 'La reseña no pertenece a tu local.');
        }

        return redirect()->route('reviews.index')
            ->with('success', 'Respuesta guardada correctamente.');
    }
}