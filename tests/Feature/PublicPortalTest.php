<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_uses_the_cowapp_content_and_shows_only_published_news(): void
    {
        News::create([
            'title' => 'Manejo responsable del ganado',
            'slug' => 'manejo-responsable',
            'content' => 'Recomendaciones para el cuidado diario del ganado.',
            'excerpt' => 'Guía práctica para ganaderos.',
            'published' => true,
        ]);
        News::create([
            'title' => 'Borrador interno',
            'slug' => 'borrador-interno',
            'content' => 'Este texto no debe aparecer en el portal.',
            'published' => false,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Tu ganadería, organizada desde el primer lote.')
            ->assertSee('Gestión de lotes')
            ->assertSee('Manejo responsable del ganado')
            ->assertSee('Guía práctica para ganaderos.')
            ->assertDontSee('Borrador interno')
            ->assertSee(route('contact.send'), false);
    }

    public function test_portal_has_an_editorial_empty_state_when_there_are_no_published_posts(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Pronto encontrarás nuevas publicaciones');
    }
}
