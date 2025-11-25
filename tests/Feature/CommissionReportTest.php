<?php

use App\Repositories\Eloquent\EloquentCommissionRepository;
use function Pest\Laravel\get;
use Inertia\Testing\AssertableInertia;

describe('Commission Report Tests (using repository)', function () {

    function fetchInvoice(string $invoice): array
    {
        $repo = new EloquentCommissionRepository();

        // Query using your repository directly
        $row = $repo->queryReport(['invoice' => $invoice])->first();

        expect($row)->not->toBeNull();

        // Cast to array so you can use same assertions
        return (array) $row;
    }

    it('checks commission for invoice ABC4170 = 6.00', function () {
        $row = fetchInvoice('ABC4170');
        expect(number_format($row['commission'], 2))->toBe('6.00');
    });

    it('checks commission for invoice ABC6931 = 37.20', function () {
        $row = fetchInvoice('ABC6931');
        expect(number_format($row['commission'], 2))->toBe('37.20');
    });

    it('checks commission for invoice ABC23352 = 27.60', function () {
        $row = fetchInvoice('ABC23352');
        expect(number_format($row['commission'], 2))->toBe('27.60');
    });

    it('checks commission for invoice ABC3010 = 0', function () {
        $row = fetchInvoice('ABC3010');
        expect(number_format($row['commission'], 2))->toBe('0.00');
    });

    it('checks commission for invoice ABC19323 = 0', function () {
        $row = fetchInvoice('ABC19323');
        expect(number_format($row['commission'], 2))->toBe('0.00');
    });
});
