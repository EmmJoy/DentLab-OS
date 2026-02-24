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
use App\Models\PaymentPlan;
use App\Models\PaymentPlanPayment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PatientsExport;
use App\Exports\PaymentsExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'total_inventory' => Inventory::count(),
            'low_stock_items' => Inventory::where('quantity', '<', 10)->count(),
            'today_payments' => Payment::whereDate('payment_date', today())->sum('amount'),
            'recent_patients' => Patient::latest()->take(5)->get(),
        ];

        // ===== Dashboard Analytics (last 12 months) =====
        $start = Carbon::now()->startOfMonth()->subMonths(11);
        $months = collect(range(0, 11))
            ->map(fn($i) => $start->copy()->addMonths($i))
            ->values();
        $labels = $months->map(fn($m) => $m->format('M Y'));

        // Patients per month by case_type (stacked)
        $caseTypes = ['Deep CBCD','Full Case','Short Case','Single ARC','Retainer'];
        $rawPatients = DB::table('patients')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, case_type, COUNT(*) as c")
            ->where('created_at', '>=', $start)
            ->groupBy('ym','case_type')
            ->get();
        $patientsByMonth = [];
        foreach ($caseTypes as $ct) { $patientsByMonth[$ct] = array_fill(0, 12, 0); }
        foreach ($rawPatients as $row) {
            $idx = $months->search(fn($m) => $m->format('Y-m') === $row->ym);
            if ($idx !== false && in_array($row->case_type, $caseTypes, true)) {
                $patientsByMonth[$row->case_type][$idx] = (int) $row->c;
            }
        }

        // Payments per month (line) from payment plan payments
        $rawPays = DB::table('payment_plan_payments')
            ->selectRaw("DATE_FORMAT(payment_date, '%Y-%m') as ym, SUM(amount) as total")
            ->where('payment_date', '>=', $start)
            ->groupBy('ym')
            ->get();
        $paymentsByMonth = array_fill(0, 12, 0.0);
        foreach ($rawPays as $row) {
            $idx = $months->search(fn($m) => $m->format('Y-m') === $row->ym);
            if ($idx !== false) { $paymentsByMonth[$idx] = (float) $row->total; }
        }

        $charts = [
            'labels' => $labels,
            'patients' => $patientsByMonth,
            'payments' => $paymentsByMonth,
        ];

        return view('admin.dashboard', compact('stats','charts','caseTypes'));
    }

    // ===== Payments: Shortlist (last 15 days) =====
    public function paymentsShortlistLast15()
    {
        $since = Carbon::now()->subDays(15);
        $payments = PaymentPlanPayment::query()
            ->join('payment_plans as pp', 'pp.id', '=', 'payment_plan_payments.payment_plan_id')
            ->join('patients as pat', 'pat.Predict3DId', '=', 'pp.predict3d_id')
            ->leftJoin('users as u', 'u.id', '=', 'payment_plan_payments.created_by')
            ->whereDate('payment_plan_payments.payment_date', '>=', $since->toDateString())
            ->orderByDesc('payment_plan_payments.payment_date')
            ->select([
                'payment_plan_payments.id as id',
                'payment_plan_payments.amount as amount',
                'payment_plan_payments.payment_method as payment_method',
                'payment_plan_payments.payment_date as payment_date',
                'pp.id as plan_id',
                'pp.predict3d_id as predict3d_id',
                'pat.FullName as patient_full_name',
                'u.name as processor_name',
            ])->get();

        return view('admin.payments.shortlist', [
            'since' => $since,
            'payments' => $payments,
        ]);
    }

    // ===== Payments: Export all (Excel, colorful columns) =====
    public function exportAllPayments()
    {
        $data = PaymentPlanPayment::query()
            ->join('payment_plans as pp', 'pp.id', '=', 'payment_plan_payments.payment_plan_id')
            ->join('patients as pat', 'pat.Predict3DId', '=', 'pp.predict3d_id')
            ->leftJoin('users as u', 'u.id', '=', 'payment_plan_payments.created_by')
            ->orderByDesc('payment_plan_payments.payment_date')
            ->select([
                'payment_plan_payments.amount as amount',
                'payment_plan_payments.payment_method as payment_method',
                'payment_plan_payments.payment_date as payment_date',
                'pp.id as plan_id',
                'pp.predict3d_id as predict3d_id',
                'pp.total_amount as total_amount',
                'pp.remaining_amount as remaining_amount',
                'pp.is_installment as is_installment',
                'pp.next_payment_date as next_payment_date',
                'pat.FullName as patient_full_name',
                'pat.PhoneNumber as patient_phone',
                'u.name as processor_name',
            ])->get();

        $fileName = 'payments_all_'.now()->format('Ymd_His').'.xlsx';
        return Excel::download(new PaymentsExport($data, 'All Payments'), $fileName);
    }

    public function exportPaymentsRange(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $from = Carbon::parse($validated['from'])->startOfDay();
        $to = Carbon::parse($validated['to'])->endOfDay();
        if ($from->gt($to)) { [$from, $to] = [$to, $from]; }

        $data = PaymentPlanPayment::query()
            ->join('payment_plans as pp', 'pp.id', '=', 'payment_plan_payments.payment_plan_id')
            ->join('patients as pat', 'pat.Predict3DId', '=', 'pp.predict3d_id')
            ->leftJoin('users as u', 'u.id', '=', 'payment_plan_payments.created_by')
            ->whereBetween('payment_plan_payments.payment_date', [$from, $to])
            ->orderByDesc('payment_plan_payments.payment_date')
            ->select([
                'payment_plan_payments.amount as amount',
                'payment_plan_payments.payment_method as payment_method',
                'payment_plan_payments.payment_date as payment_date',
                'pp.id as plan_id',
                'pp.predict3d_id as predict3d_id',
                'pp.total_amount as total_amount',
                'pp.remaining_amount as remaining_amount',
                'pp.is_installment as is_installment',
                'pp.next_payment_date as next_payment_date',
                'pat.FullName as patient_full_name',
                'pat.PhoneNumber as patient_phone',
                'u.name as processor_name',
            ])->get();

        $label = 'Payments from '.$from->format('Y-m-d').' to '.$to->format('Y-m-d');
        $fileName = 'payments_'.$from->format('Ymd').'_to_'.$to->format('Ymd').'_'.now()->format('His').'.xlsx';
        return Excel::download(new PaymentsExport($data, $label), $fileName);
    }

    // ===== Payments: Pending installments (unpaid remaining) =====
    public function paymentsInstallmentsPending()
    {
        $plans = PaymentPlan::query()
            ->join('patients as pat', 'pat.Predict3DId', '=', 'payment_plans.predict3d_id')
            ->where('payment_plans.is_installment', true)
            ->where(function($q){
                $q->whereNull('payment_plans.remaining_amount')
                  ->orWhere('payment_plans.remaining_amount', '>', 0);
            })
            ->orderBy('payment_plans.next_payment_date', 'asc')
            ->select([
                'payment_plans.id as plan_id',
                'payment_plans.predict3d_id',
                'payment_plans.total_amount',
                'payment_plans.remaining_amount',
                'payment_plans.payment_method',
                'payment_plans.next_payment_date',
                'pat.FullName as patient_full_name',
                DB::raw('(select COALESCE(sum(p2.amount),0) from payment_plan_payments p2 where p2.payment_plan_id = payment_plans.id) as total_paid'),
            ])->get();

        return view('admin.payments.installments', [
            'plans' => $plans,
        ]);
    }

    // --- Utility: Serve files from public storage (helps on Windows/XAMPP when symlink is problematic)
    public function servePublic(string $path)
    {
        $path = ltrim($path, '/');
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        if (!$disk->exists($path)) {
            abort(404);
        }
        $absolute = storage_path('app/public/' . $path);
        $mime = \Illuminate\Support\Facades\File::mimeType($absolute) ?: 'application/octet-stream';
        return response()->file($absolute, [ 'Content-Type' => $mime ]);
    }

    // Patient Management
    public function patients()
    {
        $patients = Patient::with('creator')->paginate(10);
        return view('admin.patients.index', compact('patients'));
    }

    // ===== Shortlist (last 15 days) =====
    public function shortlistPatients()
    {
        $since = Carbon::now()->subDays(15);
        $patients = Patient::where('created_at', '>=', $since)
            ->orderByDesc('created_at')
            ->get(['Predict3DId','FullName','PhoneNumber','Gender','DateOfBirth','DoctorName','doctor_email','created_at']);
        return view('admin.patients.shortlist', [
            'type' => 'patients',
            'since' => $since,
            'patients' => $patients,
            'doctors' => collect(),
        ]);
    }

    public function shortlistDoctors()
    {
        $since = Carbon::now()->subDays(15);
        $doctors = Patient::where('created_at', '>=', $since)
            ->orderByDesc('created_at')
            ->get(['DoctorName','doctor_email','created_at'])
            ->unique(function($row){ return $row->DoctorName.'|'.$row->doctor_email; })
            ->values();
        return view('admin.patients.shortlist', [
            'type' => 'doctors',
            'since' => $since,
            'patients' => collect(),
            'doctors' => $doctors,
        ]);
    }

    // ===== Exports =====
    public function exportPatientsAll()
    {
        $data = Patient::orderBy('created_at', 'desc')->get();
        $fileName = 'patients_all_'.now()->format('Ymd_His').'.xlsx';
        return Excel::download(new PatientsExport($data, 'All Patients'), $fileName);
    }

    public function exportPatientsLastMonth()
    {
        $since = Carbon::now()->subDays(30);
        $data = Patient::where('created_at', '>=', $since)->orderBy('created_at', 'desc')->get();
        $fileName = 'patients_last_month_'.now()->format('Ymd_His').'.xlsx';
        return Excel::download(new PatientsExport($data, 'Last 30 Days Patients'), $fileName);
    }

    public function exportPatientsRange(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $from = Carbon::parse($validated['from'])->startOfDay();
        $to = Carbon::parse($validated['to'])->endOfDay();
        if ($from->gt($to)) { [$from, $to] = [$to, $from]; }

        $data = Patient::whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'desc')
            ->get();

        $label = 'Patients from '.$from->format('Y-m-d').' to '.$to->format('Y-m-d');
        $fileName = 'patients_'.$from->format('Ymd').'_to_'.$to->format('Ymd').'_'.now()->format('His').'.xlsx';
        return Excel::download(new PatientsExport($data, $label), $fileName);
    }

    public function createPatient()
    {
        return view('admin.patients.create');
    }

    public function storePatient(Request $request)
    {
        $request->validate([
            'Predict3DId' => 'required|string|max:255|unique:patients,Predict3DId',
            'FullName' => 'required|string|max:255',
            'ScanningFor' => 'required|in:Aligner,Zirconia,Others',
            'ScanningForOthers' => 'required_if:ScanningFor,Others|nullable|string|max:255',
            'case_type' => 'required|in:Deep CBCD,Full Case,Short Case,Single ARC,Retainer',
            'DoctorName' => 'required|string|max:255',
            'doctor_email' => 'nullable|email',
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
            'case_type' => $request->case_type,
            'DoctorName' => $request->DoctorName,
            'doctor_email' => $request->doctor_email,
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

        return redirect()->route('admin.patients.index')
                        ->with('success', 'Patient added successfully.');
    }

    public function showPatient(Patient $patient)
    {
        $patient->load('creator', 'payments');
        return view('admin.patients.show', compact('patient'));
    }

    public function editPatient(Patient $patient)
    {
        return view('admin.patients.edit', compact('patient'));
    }

    public function updatePatient(Request $request, Patient $patient)
    {
        // Log the update attempt with detailed information
        \Log::info('=== UPDATE PATIENT START ===');
        \Log::info('Patient ID from route: ' . $patient->id);
        \Log::info('Patient Predict3DId: ' . $patient->Predict3DId);
        \Log::info('Request method: ' . $request->method());
        \Log::info('Request URL: ' . $request->fullUrl());
        \Log::info('Request data:', $request->all());
        \Log::info('Current patient data:', $patient->toArray());

        // Validate the request data
        try {
            $validated = $request->validate([
            'Predict3DId' => 'required|string|max:255|unique:patients,Predict3DId,' . $patient->Predict3DId . ',Predict3DId',
            'FullName' => 'required|string|max:255',
            'ScanningFor' => 'required|in:Aligner,Zirconia,Others',
            'ScanningForOthers' => 'required_if:ScanningFor,Others|nullable|string|max:255',
            'case_type' => 'required|in:Deep CBCD,Full Case,Short Case,Single ARC,Retainer',
            'DoctorName' => 'required|string|max:255',
            'doctor_email' => 'nullable|email',
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

            \Log::info('Validation passed', ['validated_data' => $validated]);

            $updateData = [
                'Predict3DId' => $request->Predict3DId,
                'FullName' => $request->FullName,
                'ScanningFor' => $request->ScanningFor,
                'ScanningForOthers' => $request->ScanningForOthers,
                'case_type' => $request->case_type,
                'DoctorName' => $request->DoctorName,
                'doctor_email' => $request->doctor_email,
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
            ];

            \Log::info('Attempting to update patient with data:', $updateData);
            
            // Try updating using save() instead of update() for better error reporting
            foreach ($updateData as $key => $value) {
                $patient->$key = $value;
            }
            
            $saved = $patient->save();
            
            if ($saved) {
                $updatedPatient = $patient->fresh();
                \Log::info('Patient updated successfully', $updatedPatient->toArray());
                return redirect()->route('admin.patients.show', $patient)
                    ->with('success', 'Patient updated successfully.');
            } else {
                $error = 'Failed to save patient record. No database error but save() returned false.';
                \Log::error($error);
                throw new \Exception($error);
            }
        } catch (\Illuminate\Validation\ValidationException $ve) {
            // Log validation errors
            \Log::error('Validation failed', [
                'errors' => $ve->errors(),
                'input' => $request->all()
            ]);
            throw $ve; // Re-throw to let Laravel handle the validation response
            
        } catch (\Exception $e) {
            // Log detailed error information
            $errorMessage = 'Error updating patient: ' . $e->getMessage();
            \Log::error($errorMessage, [
                'exception' => get_class($e),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'patient_id' => $patient->id ?? 'unknown',
                'predict3d_id' => $patient->Predict3DId ?? 'unknown'
            ]);
            
            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Failed to update patient. Please try again or contact support if the problem persists.',
                    'details' => $e->getMessage()
                ]);
        }
    }

    public function deletePatient(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('admin.patients.index')
                        ->with('success', 'Patient deleted successfully.');
    }

    // Inventory Management
    public function inventory()
    {
        $products = Product::with('creator', 'requester')->paginate(12);
        $pendingRequests = ProductRequest::with('requester')->where('status', 'pending')->get();
        return view('admin.inventory.index', compact('products', 'pendingRequests'));
    }

    public function createProduct()
    {
        return view('admin.inventory.create');
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'image' => $imagePath,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.inventory.index')
                        ->with('success', 'Product added successfully.');
    }

    public function showProduct(Product $product)
    {
        return view('admin.inventory.show', compact('product'));
    }

    public function editProduct(Product $product)
    {
        return view('admin.inventory.edit', compact('product'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.inventory.index')
                        ->with('success', 'Product updated successfully.');
    }

    public function deleteProduct(Product $product)
    {
        // Delete image if exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.inventory.index')
                        ->with('success', 'Product deleted successfully.');
    }

    public function approveProductRequest(ProductRequest $productRequest)
    {
        try {
            // Lookup requester safely (may be null if bad data)
            $requester = User::find($productRequest->requested_by);
            $requesterName = $requester ? $requester->name : 'Unknown';

            // Create product from request
            $imagePath = $productRequest->image;

            Product::create([
                'name' => $productRequest->name,
                'price' => $productRequest->price,
                'quantity' => $productRequest->quantity,
                'description' => trim(($productRequest->description ?? '') . "\n\n[Requested by: {$requesterName} - Lab Technician]"),
                'image' => $imagePath,
                'status' => 'active',
                'created_by' => auth()->id(),
                'requested_by' => $productRequest->requested_by,
            ]);

            // Mark request as approved
            $productRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            return redirect()->route('admin.inventory.index')
                ->with('success', 'Product request approved and added to inventory. Lab Technician: ' . $requesterName);
        } catch (\Exception $e) {
            return redirect()->route('admin.inventory.index')
                ->with('error', 'Error approving request: ' . $e->getMessage());
        }
    }

    public function rejectProductRequest(Request $requestData, ProductRequest $request)
    {
        $requestData->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $request->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $requestData->rejection_reason,
        ]);

        return redirect()->route('admin.inventory.index')
                        ->with('success', 'Product request rejected.');
    }

    // Payment Management
    public function payments()
    {
        $payments = \App\Models\PaymentPlanPayment::query()
            ->join('payment_plans as pp', 'pp.id', '=', 'payment_plan_payments.payment_plan_id')
            ->join('patients as pat', 'pat.Predict3DId', '=', 'pp.predict3d_id')
            ->leftJoin('users as u', 'u.id', '=', 'payment_plan_payments.created_by')
            ->orderByDesc('payment_plan_payments.payment_date')
            ->select([
                'payment_plan_payments.id as id',
                'payment_plan_payments.amount as amount',
                'payment_plan_payments.payment_method as payment_method',
                'payment_plan_payments.payment_date as payment_date',
                'pp.id as plan_id',
                'pp.predict3d_id as predict3d_id',
                'pp.total_amount as total_amount',
                'pp.remaining_amount as remaining_amount',
                'pp.is_installment as is_installment',
                'pp.next_payment_date as next_payment_date',
                'pat.FullName as patient_full_name',
                'pat.PhoneNumber as patient_phone',
                'pat.status as patient_status',
                'u.name as processor_name',
                \DB::raw('(select COALESCE(sum(p2.amount),0) from payment_plan_payments p2 where p2.payment_plan_id = pp.id) as total_paid'),
            ])
            ->paginate(10);

        // Summary totals
        $totalThisMonth = \App\Models\PaymentPlanPayment::whereDate('payment_date', '>=', now()->startOfMonth())->sum('amount');
        $totalToday = \App\Models\PaymentPlanPayment::whereDate('payment_date', today())->sum('amount');
        $grandTotal = \App\Models\PaymentPlanPayment::sum('amount');

        return view('admin.payments.index', [
            'payments' => $payments,
            'totalThisMonth' => $totalThisMonth,
            'totalToday' => $totalToday,
            'grandTotal' => $grandTotal,
        ]);
    }

    public function createPayment()
    {
        $patients = Patient::where('status', 'active')->get();
        return view('admin.payments.create', compact('patients'));
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            // patients now use Predict3DId (string) as primary key
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

        return redirect()->route('admin.payments.index')->with('success', 'Payment recorded successfully!');
    }

    // Lab Technician Management
    public function labTechnicians()
    {
        $technicians = User::where('role', 'lab_technician')->paginate(10);
        return view('admin.lab-technicians.index', compact('technicians'));
    }

    public function createLabTechnician()
    {
        return view('admin.lab-technicians.create');
    }

    public function storeLabTechnician(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'lab_technician',
            'status' => 'active',
        ]);

        // Flash credentials so admin can see them once
        return redirect()->route('admin.lab-technicians.index')->with([
            'success' => 'Lab Technician created successfully!',
            'labtech_created' => [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'password' => $request->password, // show once; we do NOT store plaintext
            ],
        ]);
    }

    public function editLabTechnician(User $user)
    {
        if ($user->role !== 'lab_technician') {
            abort(404);
        }
        return view('admin.lab-technicians.edit', compact('user'));
    }

    public function updateLabTechnician(Request $request, User $user)
    {
        if ($user->role !== 'lab_technician') {
            abort(404);
        }
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'status' => 'required|in:active,inactive',
        ]);

        $update = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'status' => $data['status'],
        ];
        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }
        $user->update($update);

        return redirect()->route('admin.lab-technicians.index')->with('success', 'Lab Technician updated successfully!');
    }

    public function deleteLabTechnician(User $user)
    {
        if ($user->role !== 'lab_technician') {
            abort(404);
        }
        try {
            $user->delete();
            return redirect()->route('admin.lab-technicians.index')->with('success', 'Lab Technician deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            // Likely foreign key constraint (e.g., product_requests.requested_by)
            return redirect()->route('admin.lab-technicians.index')
                ->with('error', 'Cannot delete this Lab Technician because there are linked records (e.g., product requests). Reassign or remove those records first.');
        }
    }

    // Data Entry Portal
    public function dataEntry()
    {
        return view('lab.data-entry');
    }

    // ===================== Payments: Predict3DId-based Plan =====================
    public function paymentPlanIndex()
    {
        return view('admin.payments.plan');
    }

    public function getPaymentPlan(string $predict3dId)
    {
        $plan = PaymentPlan::where('predict3d_id', $predict3dId)->first();
        $payments = [];
        $paid = 0;
        if ($plan) {
            $payments = PaymentPlanPayment::where('payment_plan_id', $plan->id)
                ->orderBy('payment_date', 'asc')
                ->get();
            $paid = (float) $payments->sum('amount');
        }

        return response()->json([
            'plan' => $plan,
            'payments' => $payments,
            'paid' => $paid,
            'remaining' => $plan ? (float) $plan->total_amount - $paid : null,
        ]);
    }

    public function savePaymentPlan(Request $request, string $predict3dId)
    {
        $data = $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer',
            'is_installment' => 'required|boolean',
            'current_payment_amount' => 'nullable|numeric|min:0',
            'current_payment_date' => 'nullable|date',
            // When installment, next payment date is mandatory
            'next_payment_date' => 'required_if:is_installment,1|date',
        ], [
            'next_payment_date.required_if' => 'Next payment date is required when installment is selected.',
        ]);

        $plan = PaymentPlan::firstOrNew(['predict3d_id' => $predict3dId]);
        $plan->predict3d_id = $predict3dId;
        $plan->total_amount = $data['total_amount'];
        $plan->payment_method = $data['payment_method'];
        $plan->is_installment = (bool) $data['is_installment'];
        $plan->next_payment_date = $data['next_payment_date'] ?? null;

        // Compute remaining amount: total - sum(payments) - optionally current payment
        $existingPaid = 0;
        if ($plan->exists) {
            $existingPaid = (float) PaymentPlanPayment::where('payment_plan_id', $plan->id)->sum('amount');
        }
        $currentPaid = (float) ($data['current_payment_amount'] ?? 0);
        $plan->remaining_amount = max(0, (float) $plan->total_amount - $existingPaid - $currentPaid);
        $plan->created_by = auth()->id();
        $plan->save();

        // If plan is now fully paid, mark patient as inactive
        if ((float) $plan->remaining_amount === 0.0) {
            if ($plan->predict3d_id) {
                $p = Patient::where('Predict3DId', $plan->predict3d_id)->first();
                if ($p && $p->status !== 'inactive') {
                    $p->status = 'inactive';
                    $p->save();
                }
            }
        }

        // If there is a current payment amount, record it
        if ($currentPaid > 0) {
            PaymentPlanPayment::create([
                'payment_plan_id' => $plan->id,
                'amount' => $currentPaid,
                'payment_date' => $data['current_payment_date'] ?? now()->toDateString(),
                'payment_method' => $data['payment_method'],
                'created_by' => auth()->id(),
            ]);
        }

        // Recompute paid/remaining
        $paid = (float) PaymentPlanPayment::where('payment_plan_id', $plan->id)->sum('amount');
        $plan->remaining_amount = max(0, (float) $plan->total_amount - $paid);
        $plan->save();

        // If plan is fully paid after this installment, mark patient as inactive
        if ((float) $plan->remaining_amount === 0.0) {
            if ($plan->predict3d_id) {
                $p = Patient::where('Predict3DId', $plan->predict3d_id)->first();
                if ($p && $p->status !== 'inactive') {
                    $p->status = 'inactive';
                    $p->save();
                }
            }
        }

        return response()->json([
            'success' => true,
            'plan' => $plan,
            'payments' => PaymentPlanPayment::where('payment_plan_id', $plan->id)->orderBy('payment_date')->get(),
        ]);
    }

    public function addInstallmentPayment(Request $request, string $predict3dId)
    {
        // Fetch plan first to know if this is an installment plan (create if missing)
        $plan = PaymentPlan::where('predict3d_id', $predict3dId)->first();
        if (!$plan) {
            $plan = new PaymentPlan();
            $plan->predict3d_id = $predict3dId;
            $plan->total_amount = (float) ($request->input('total_amount') ?? $request->input('amount') ?? 0);
            $plan->payment_method = $request->input('payment_method', 'cash');
            $plan->is_installment = false; // default to full payment if created via this endpoint
            $plan->next_payment_date = null;
            $plan->remaining_amount = $plan->total_amount; // will be recomputed below after inserting payment
            $plan->created_by = auth()->id();
            $plan->save();
        }

        $rules = [
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer',
            'next_payment_date' => ($plan->is_installment ? 'required|date' : 'nullable|date'),
        ];
        $messages = [
            'next_payment_date.required' => 'Next payment date is required for installment plans.',
        ];
        $data = $request->validate($rules, $messages);

        PaymentPlanPayment::create([
            'payment_plan_id' => $plan->id,
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'payment_method' => $data['payment_method'],
            'created_by' => auth()->id(),
        ]);

        // Update next date and remaining
        $plan->next_payment_date = $data['next_payment_date'] ?? $plan->next_payment_date;
        $paid = (float) PaymentPlanPayment::where('payment_plan_id', $plan->id)->sum('amount');
        $plan->remaining_amount = max(0, (float) $plan->total_amount - $paid);
        $plan->save();

        return response()->json([
            'success' => true,
            'plan' => $plan,
            'payments' => PaymentPlanPayment::where('payment_plan_id', $plan->id)->orderBy('payment_date')->get(),
        ]);
    }

    public function updatePlanTotal(Request $request, string $predict3dId)
    {
        // Only Admins can update total
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'total_amount' => 'required|numeric|min:0',
        ]);
        // Find or create plan to avoid 404 when updating total before plan exists
        $plan = PaymentPlan::firstOrNew(['predict3d_id' => $predict3dId]);
        if (!$plan->exists) {
            $plan->predict3d_id = $predict3dId;
            $plan->payment_method = $plan->payment_method ?: 'cash';
            $plan->is_installment = (bool) ($plan->is_installment ?? false);
            $plan->next_payment_date = $plan->next_payment_date ?? null;
            $plan->remaining_amount = $plan->remaining_amount ?? 0;
            $plan->created_by = auth()->id();
        }
        $paid = (float) PaymentPlanPayment::where('payment_plan_id', $plan->id)->sum('amount');
        if ($data['total_amount'] < $paid) {
            return response()->json(['message' => 'Total cannot be less than amount already paid'], 422);
        }
        $plan->total_amount = $data['total_amount'];
        $plan->remaining_amount = max(0, (float) $plan->total_amount - $paid);
        $plan->save();
        return response()->json(['success' => true, 'plan' => $plan]);
    }
    public function productionIndex()
    {
        return view('admin.production.index');
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
     * Save patient cases (Upper and Lower)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $predict3dId
     * @return \Illuminate\Http\JsonResponse
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
            'patient' => $patient
        ]);
    }

    /**
     * Get production steps for a patient by Predict3DId
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
     * Save production steps for a patient by Predict3DId
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
}
