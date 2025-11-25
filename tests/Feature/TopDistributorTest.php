<?php

use function Pest\Laravel\get;
use Inertia\Testing\AssertableInertia;

describe('Top Distributors Inertia Tests', function () {

    it('checks Demario Purdy total = 22026.75', function () {
        $rows = [];

        get('/reports/top-distributors?limit=200')
            ->assertStatus(200)
            ->assertInertia(fn (AssertableInertia $page) =>
                $page->component('TopDistributors/Index')
                     ->has('results', fn ($top) => $rows = $top) // capture props
            );

        $match = collect($rows)->firstWhere('distributor_name', 'Demario Purdy');

        expect($match)->not->toBeNull();
        expect(number_format($match['total_sales'], 2))->toBe('22026.75');
        expect($match['rank'])->toBe(1);
    });

    it('checks Floy Miller total = 9645.00', function () {
        $rows = [];

        get('/reports/top-distributors?limit=200')
            ->assertStatus(200)
            ->assertInertia(fn (AssertableInertia $page) =>
                $page->component('TopDistributors/Index')
                     ->has('results', fn ($top) => $rows = $top)
            );

        $match = collect($rows)->firstWhere('distributor_name', 'Floy Miller');

        expect($match)->not->toBeNull();
        expect(number_format($match['total_sales'], 2))->toBe('9645.00');
    });

    it('checks Loy Schamberger total = 575.00', function () {
        $rows = [];

        get('/reports/top-distributors?limit=200')
            ->assertStatus(200)
            ->assertInertia(fn (AssertableInertia $page) =>
                $page->component('TopDistributors/Index')
                     ->has('results', fn ($top) => $rows = $top)
            );

        $match = collect($rows)->firstWhere('distributor_name', 'Loy Schamberger');

        expect($match)->not->toBeNull();
        expect(number_format($match['total_sales'], 2))->toBe('575.00');
    });

    it('checks rank ties for #197 (Chaim Kuhn & Eliane Bogisich)', function () {
        $rows = [];

        get('/reports/top-distributors?limit=200')
            ->assertStatus(200)
            ->assertInertia(fn (AssertableInertia $page) =>
                $page->component('TopDistributors/Index')
                     ->has('results', fn ($top) => $rows = $top)
            );

        $chaim  = collect($rows)->firstWhere('distributor_name', 'Chaim Kuhn');
        $eliane = collect($rows)->firstWhere('distributor_name', 'Eliane Bogisich');

        expect($chaim)->not->toBeNull();
        expect($eliane)->not->toBeNull();

        expect(number_format($chaim['total_sales'], 2))->toBe('360.00');
        expect(number_format($eliane['total_sales'], 2))->toBe('360.00');

        expect($chaim['rank'])->toBe($eliane['rank']);
        expect($chaim['rank'])->toBe(197);
    });

});
