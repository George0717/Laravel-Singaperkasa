<?php
namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::with('details')->get();
        return view('sales_orders.index', compact('salesOrders'));
    }

    public function create()
    {
        return view('sales_orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'required|string|max:255',
            'po_date' => 'required|date',
            'po_number' => 'required|string|max:255',
            'so_number' => 'nullable|string|max:255',
            'discount' => 'required|numeric',
            'down_payment' => 'required|numeric',
            'vat' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'payment_schedule_type' => 'required|string'
        ]);

        $salesOrder = SalesOrder::create($request->all());

        // Assuming you also have a way to handle items in the request
        foreach ($request->items as $item) {
            SalesOrderDetail::create([
                'sales_order_id' => $salesOrder->id,
                'item_name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['quantity'] * $item['price']
            ]);
        }

        return redirect()->route('sales_orders.index');
    }

    public function show(SalesOrder $salesOrder)
    {
        return view('sales_orders.show', compact('salesOrder'));
    }

    public function edit(SalesOrder $salesOrder)
    {
        return view('sales_orders.edit', compact('salesOrder'));
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'required|string|max:255',
            'po_date' => 'required|date',
            'po_number' => 'required|string|max:255',
            'so_number' => 'nullable|string|max:255',
            'discount' => 'required|numeric',
            'down_payment' => 'required|numeric',
            'vat' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'payment_schedule_type' => 'required|string'
        ]);

        $salesOrder->update($request->all());

        // Update details as needed

        return redirect()->route('sales_orders.index');
    }

    public function destroy(SalesOrder $salesOrder)
    {
        $salesOrder->delete();
        return redirect()->route('sales_orders.index');
    }
}
