<?php

use App\Repositories\Eloquent\EloquentTopDistributorRepository;

describe('Top Distributor Tests (using repository)', function () {

function fetchDistributor(string $name): array
{
    $repo = new EloquentTopDistributorRepository();

    $rows = $repo->queryTopDistributors()
        ->orderByDesc('total_sales')
        ->get();

    $row = $rows->first(fn($r) => trim($r->distributor_name) === $name);

    expect($row)->not->toBeNull("Distributor '$name' not found in top distributors");

    return (array) $row;
}


    it('checks Demario Purdy total = 22026.75', function () {
        $row = fetchDistributor('Demario Purdy');

        expect(number_format($row['total_sales'], 2))->toBe('22,026.75');

        expect(isset($row['rank']))->toBeTrue("`rank` column missing from query");
    });

    it('checks Floy Miller total = 9645.00', function () {
        $row = fetchDistributor('Floy Miller');

        expect(number_format($row['total_sales'], 2))->toBe('9,645.00');
    });

    it('checks Loy Schamberger total = 575.00', function () {
        $row = fetchDistributor('Loy Schamberger');

        expect(number_format($row['total_sales'], 2))->toBe('575.00');
    });

 it('checks rank ties for same total_sales', function () {
    $chaim  = fetchDistributor('Chaim Kuhn');
    $eliane = fetchDistributor('Eliane Bogisich');

    expect(number_format($chaim['total_sales'], 2))->toBe('360.00');
    expect(number_format($eliane['total_sales'], 2))->toBe('360.00');

    expect(isset($chaim['rank']))->toBeTrue("`rank` missing");
    expect(isset($eliane['rank']))->toBeTrue("`rank` missing");

    // Ensure both ranks are equal (tied)
    expect($chaim['rank'])->toBe($eliane['rank']);
});


});
