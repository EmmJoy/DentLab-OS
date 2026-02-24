<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductRequest;
use App\Models\User;
use App\Models\ProductionStep;
use Illuminate\Support\Facades\Schema;

class LabTechnicianController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'low_stock_items' => Inventory::where('quantity', '<', 10)->count(),
            'today_payments' => Payment::whereDate('payment_date', today())->sum('amount'),
            'recent_patients' => Patient::latest()->take(5)->get(),
        ];

        return view('lab.dashboard', compact('stats'));
    }

    // Patient Management (Read-only)
    public function patients()
    {
        $patients = Patient::with('creator')
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);
        
        return view('lab.patients.index', compact('patients'));
    }

    public function createPatient()
    {
        return view('lab.patients.create');
    }

    public function storePatient(Request $request)
    {
        $request->validate([
            'Predict3DId' => 'required|string|max:255|unique:patients,Predict3DId',
            'FullName' => 'required|string|max:255',
            'ScanningFor' => 'required|in:Aligner,Zirconia,Others',
            'ScanningForOthers' => 'required_if:ScanningFor,Others|nullable|string|max:255',
            'DoctorName' => 'required|string|max:255',
            'ChamberName' => 'required|string|max:255',
            'TerritoryName' => 'required|string|max:255',
            'RegionalName' => 'required|string|max:255',
            'PhoneNumber' => 'required|string|max:13',
            'EmergencyContact' => 'required|string|max:13',
            'Gender' => 'required|in:Male,Female,Custom',
            'DateOfBirth' => 'required|date',
            'Address' => 'required|string',
            'UpperCases' => 'nullable|integer|min:0',
            'LowerCases' => 'nullable|integer|min:0',
        ]);

        Patient::create([
            'Predict3DId' => $request->Predict3DId,
            'FullName' => $request->FullName,
            'ScanningFor' => $request->ScanningFor,
            'ScanningForOthers' => $request->ScanningForOthers,
            'DoctorName' => $request->DoctorName,
            'ChamberName' => $request->ChamberName,
            'TerritoryName' => $request->TerritoryName,
            'RegionalName' => $request->RegionalName,
            'PhoneNumber' => $request->PhoneNumber,
            'EmergencyContact' => $request->EmergencyContact,
            'Gender' => $request->Gender,
            'DateOfBirth' => $request->DateOfBirth,
            'Address' => $request->Address,
            'UpperCases' => $request->UpperCases,
            'LowerCases' => $request->LowerCases,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('lab.patients.index')
                        ->with('success', 'Patient added successfully.');
    }

    public function showPatient(Patient $patient)
    {
        $patient->load('creator', 'payments');
        return view('lab.patients.show', compact('patient'));
    }

    public function editPatient(Patient $patient)
    {
        return view('lab.patients.edit', compact('patient'));
    }

    public function updatePatient(Request $request, Patient $patient)
    {
        $request->validate([
            'Predict3DId' => 'required|string|max:255|unique:patients,Predict3DId,' . $patient->Predict3DId . ',Predict3DId',
            'FullName' => 'required|string|max:255',
            'ScanningFor' => 'required|in:Aligner,Zirconia,Others',
            'ScanningForOthers' => 'required_if:ScanningFor,Others|nullable|string|max:255',
            'DoctorName' => 'required|string|max:255',
            'ChamberName' => 'required|string|max:255',
            'TerritoryName' => 'required|string|max:255',
            'RegionalName' => 'required|string|max:255',
            'PhoneNumber' => 'required|string|max:13',
            'EmergencyContact' => 'required|string|max:13',
            'Gender' => 'required|in:Male,Female,Custom',
            'DateOfBirth' => 'required|date',
            'Address' => 'required|string',
            'UpperCases' => 'nullable|integer|min:0',
            'LowerCases' => 'nullable|integer|min:0',
        ]);

        $patient->update([
            'Predict3DId' => $request->Predict3DId,
            'FullName' => $request->FullName,
            'ScanningFor' => $request->ScanningFor,
            'ScanningForOthers' => $request->ScanningForOthers,
            'DoctorName' => $request->DoctorName,
            'ChamberName' => $request->ChamberName,
            'TerritoryName' => $request->TerritoryName,
            'RegionalName' => $request->RegionalName,
            'PhoneNumber' => $request->PhoneNumber,
            'EmergencyContact' => $request->EmergencyContact,
            'Gender' => $request->Gender,
            'DateOfBirth' => $request->DateOfBirth,
            'Address' => $request->Address,
            'UpperCases' => $request->UpperCases,
            'LowerCases' => $request->LowerCases,
        ]);

        return redirect()->route('lab.patients.show', $patient)
                        ->with('success', 'Patient updated successfully.');
    }

    public function deletePatient(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('lab.patients.index')
                        ->with('success', 'Patient deleted successfully.');
    }

    // Product Request Management - Lab technicians can only request products
    public function requestCart()
    {
        $myRequests = ProductRequest::where('requested_by', auth()->id())
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(10);
        return view('lab.request-cart.index', compact('myRequests'));
    }

    public function createProductRequest()
    {
        return view('lab.request-cart.create');
    }

    public function storeProductRequest(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product-requests', 'public');
        }

        // Debug: Log what we're trying to save
        \Log::info('Creating ProductRequest:', [
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'image' => $imagePath,
            'status' => 'pending',
            'requested_by' => auth()->id(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name
        ]);

        $productRequest = ProductRequest::create([
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'image' => $imagePath,
            'status' => 'pending',
            'requested_by' => auth()->id(),
        ]);

        // Debug: Log what was actually saved
        \Log::info('ProductRequest created:', [
            'id' => $productRequest->id,
            'name' => $productRequest->name,
            'requested_by' => $productRequest->requested_by,
            'status' => $productRequest->status
        ]);

        return redirect()->route('lab.request-cart.index')
                        ->with('success', 'Product request submitted successfully. Waiting for admin approval.');
    }

    public function showProductRequest(ProductRequest $productRequest)
    {
        // Ensure lab technician can only view their own requests
        if ($productRequest->requested_by !== auth()->id()) {
            abort(403, 'Unauthorized access to this product request.');
        }

        return view('lab.request-cart.show', compact('productRequest'));
    }

    // Inventory Management
    public function inventory()
    {
        $inventories = Inventory::with('manager')->paginate(10);
        return view('lab.inventory.index', compact('inventories'));
    }

    public function createInventory()
    {
        return view('lab.inventory.create');
    }

    public function storeInventory(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $status = 'available';
        if ($request->quantity == 0) {
            $status = 'out_of_stock';
        } elseif ($request->expiry_date && $request->expiry_date < today()) {
            $status = 'expired';
        }

        Inventory::create([
            'item_name' => $request->item_name,
            'category' => $request->category,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'supplier' => $request->supplier,
            'expiry_date' => $request->expiry_date,
            'description' => $request->description,
            'status' => $status,
            'managed_by' => auth()->id(),
        ]);

        return redirect()->route('lab.inventory.index')->with('success', 'Inventory item added successfully!');
    }

    public function showInventory(Inventory $inventory)
    {
        $inventory->load('manager');
        return view('lab.inventory.show', compact('inventory'));
    }

    public function editInventory(Inventory $inventory)
    {
        return view('lab.inventory.edit', compact('inventory'));
    }

    public function updateInventory(Request $request, Inventory $inventory)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $status = 'available';
        if ($request->quantity == 0) {
            $status = 'out_of_stock';
        } elseif ($request->expiry_date && $request->expiry_date < today()) {
            $status = 'expired';
        }

        $inventory->update([
            'item_name' => $request->item_name,
            'category' => $request->category,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'supplier' => $request->supplier,
            'expiry_date' => $request->expiry_date,
            'description' => $request->description,
            'status' => $status,
        ]);

        return redirect()->route('lab.inventory.show', $inventory)
                        ->with('success', 'Inventory item updated successfully.');
    }

    public function deleteInventory(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('lab.inventory.index')
                        ->with('success', 'Inventory item deleted successfully.');
    }

    // Payment Management
    public function payments()
    {
        $payments = Payment::with('patient', 'processor')->paginate(10);
        return view('lab.payments.index', compact('payments'));
    }

    public function createPayment()
    {
        $patients = Patient::where('status', 'active')->get();
        return view('lab.payments.create', compact('patients'));
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            // patients use Predict3DId (string) as PK
            'patient_id' => 'required|exists:patients,Predict3DId',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer,check',
            'payment_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Payment::create([
            'patient_id' => $request->patient_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'description' => $request->description,
            'processed_by' => auth()->id(),
        ]);

        return redirect()->route('lab.payments.index')->with('success', 'Payment recorded successfully!');
    }

    public function showPayment(Payment $payment)
    {
        $payment->load('patient', 'processor');
        return view('lab.payments.show', compact('payment'));
    }

    public function editPayment(Payment $payment)
    {
        $patients = Patient::where('status', 'active')->get();
        return view('lab.payments.edit', compact('payment', 'patients'));
    }

    public function updatePayment(Request $request, Payment $payment)
    {
        $request->validate([
            // patients use Predict3DId (string) as PK
            'patient_id' => 'required|exists:patients,Predict3DId',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer,check',
            'payment_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $payment->update([
            'patient_id' => $request->patient_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'description' => $request->description,
        ]);

        return redirect()->route('lab.payments.show', $payment)
                        ->with('success', 'Payment updated successfully.');
    }

    public function deletePayment(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('lab.payments.index')
                        ->with('success', 'Payment deleted successfully.');
    }

    // Data Entry Portal
    public function dataEntry()
    {
        return view('lab.data-entry');
    }

    public function productionIndex()
    {
        return view('lab.production.index');
    }

    public function findPatientByPredictId(string $predict3dId)
    {
        $patient = \App\Models\Patient::where('Predict3DId', $predict3dId)
            ->with('payments')
            ->first();

        if(!$patient){
            return response()->json(['message' => 'Patient not found'], 404);
        }

        return response()->json($patient);
    }

    /**
     * Get production steps for a patient by Predict3DId (Lab)
     */
    public function getProductionSteps(string $predict3dId)
    {
        $steps = ProductionStep::where('predict3d_id', $predict3dId)
            ->orderBy('step_number')
            ->get(['step_number','upper_value','lower_value']);

        return response()->json([
            'predict3d_id' => $predict3dId,
            'steps' => $steps,
        ]);
    }

    /**
     * Save production steps for a patient by Predict3DId (Lab)
     */
    public function saveProductionSteps(Request $request, string $predict3dId)
    {
        $data = $request->validate([
            'steps' => 'required|array|min:1',
            'steps.*.step_number' => 'required|integer|min:1',
            'steps.*.upper' => 'nullable|integer|min:0',
            'steps.*.lower' => 'nullable|integer|min:0',
        ]);

        $hasPatientPredict = Schema::hasColumn('production_steps', 'patient_predict3d_id');
        $hasStepType = Schema::hasColumn('production_steps', 'step_type');
        $hasCreatedBy = Schema::hasColumn('production_steps', 'created_by');

        foreach ($data['steps'] as $step) {
            $values = [
                'upper_value' => $step['upper'] ?? null,
                'lower_value' => $step['lower'] ?? null,
            ];
            if ($hasPatientPredict) { $values['patient_predict3d_id'] = $predict3dId; }
            if ($hasStepType) { $values['step_type'] = 'UL'; }
            if ($hasCreatedBy) { $values['created_by'] = auth()->id() ?? 0; }

            ProductionStep::updateOrCreate(
                [
                    'predict3d_id' => $predict3dId,
                    'step_number' => (int) $step['step_number'],
                ],
                $values
            );
        }

        $saved = ProductionStep::where('predict3d_id', $predict3dId)
            ->orderBy('step_number')
            ->get(['step_number','upper_value','lower_value']);

        return response()->json([
            'success' => true,
            'predict3d_id' => $predict3dId,
            'steps' => $saved,
        ]);
    }

    /**
     * Save patient cases (Upper and Lower) for a given Predict3DId (Lab side)
     */
    public function savePatientCases(Request $request, $predict3dId)
    {
        $request->validate([
            'upper_cases' => 'required|integer|min:0',
            'lower_cases' => 'required|integer|min:0',
        ]);

        $patient = \App\Models\Patient::where('Predict3DId', $predict3dId)->firstOrFail();

        $patient->update([
            'UpperCases' => $request->upper_cases,
            'LowerCases' => $request->lower_cases,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cases updated successfully',
            'patient' => $patient->fresh(),
        ]);
    }
}
