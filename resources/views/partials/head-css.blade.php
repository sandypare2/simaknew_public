{{-- Layout JS --}}
@if(!empty($horizontal))
    <script src="{{ asset('assets/js/layout/' . $horizontal . '.js') }}"></script>
@elseif(!empty($twocolumn))
    <script src="{{ asset('assets/js/layout/' . $twocolumn . '.js') }}"></script>
@elseif(!empty($compact))
    <script src="{{ asset('assets/js/layout/' . $compact . '.js') }}"></script>
@elseif(!empty($semibox))
    <script src="{{ asset('assets/js/layout/' . $semibox . '.js') }}"></script>
@elseif(!empty($smallicon))
    <script src="{{ asset('assets/js/layout/' . $smallicon . '.js') }}"></script>
@elseif(!empty($auth))
    <script src="{{ asset('assets/js/layout/' . $auth . '.js') }}"></script>
@else
    <script src="{{ asset('assets/js/layout/layout-default.js') }}"></script>
@endif

<script src="{{ asset('assets/js/layout/layout.js') }}"></script>

{{-- CSS Dependencies --}}
<link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/simplebar/simplebar.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/icons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style">
<link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}" id="app-style">
<link rel="stylesheet" href="{{ asset('assets/css/custom.min.css') }}" id="custom-style">
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
<!-- <link rel="stylesheet" href="{{ asset('assets/libs/air-datepicker/air-datepicker.css') }}"> -->

<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"/> -->
 
<style>

/* .app-header {
  z-index: 1000 !important;
}

.app-sidebar {
  z-index: 11000 !important;
} */


    
.custom-table th,
.custom-table td {
    /* padding: 0px 10px !important;
    font-size: 12px !important;
    line-height: 1.0rem !important; */
    /* line-height: 1.2rem !important; */
    vertical-align: middle;
    /* font-family: system-ui, sans-serif !important; */
}

.custom-table th {
    padding: 0px 10px !important;
    font-size: 12px !important;
    background-color: #F5F5F5;
    font-weight: 500 !important;
    line-height: 1.6rem !important;
    color:black !important;
}
.custom-table td {
    padding: 5px 10px !important;
    font-size: 11px !important;
    font-weight: normal !important;
    line-height: 1.1rem !important;
    vertical-align: top;
    /* font-family: system-ui, sans-serif !important;     */
}   
table.dataTable td.dt-left,
table.dataTable th.dt-left {
    text-align: left !important;
}
table.dataTable td.dt-center,
table.dataTable th.dt-center {
    text-align: center !important;
}
table.dataTable td.dt-right,
table.dataTable th.dt-right {
    text-align: right !important;
}
.wrap {
    white-space: normal !important;
} 
.center {
    text-align: center !important;
}

#datatable-loader {
    transition: opacity 0.3s ease;
}
div.dataTables_processing {
    display: none !important;
}
.dataTables_wrapper.loading {
    pointer-events: none; /* disable clicks */
    opacity: 0.6;
}
.select2-container .select2-selection--single,
.select2-container .select2-selection--multiple {
    font-size: 12px; /* 🔹 Change this to your preferred size */
}
.select2-results__option {
    font-size: 12px; /* Dropdown list items */
}


/* .air-datepicker {
    border-radius: 0.475rem;
    font-size: 0.875rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    z-index: 3000 !important;
}

.air-datepicker-cell.-selected- {
    background-color: var(--bs-primary) !important;
    color: #fff !important;
    font-weight: 600;
    border-radius: 6px;
}

.air-datepicker-cell.-current- {
    background-color: var(--bs-warning-bg-subtle) !important;
    color: var(--bs-warning-text) !important;
    border-radius: 6px;
    font-weight: 600;
}

.air-datepicker-cell.-focus- {
    background-color: var(--bs-primary-bg-subtle) !important;
    color: var(--bs-primary-text) !important;
} */

.bootstrap-datepicker {
  z-index: 1200 !important; /* above modal backdrop */
}

.datepicker-dropdown {
  padding: 8px;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  background: #fff;
  box-shadow: 0 10px 20px rgba(0,0,0,0.08);
  font-family: "Inter", system-ui, -apple-system, sans-serif;
  font-size: 0.9rem;
  transition: all 0.15s ease;
}

.datepicker table {
  width: 100%;
  margin: 0;
}

.datepicker td, .datepicker th {
  width: 36px;
  height: 36px;
  text-align: center;
  border-radius: 8px;
  border: none;
  cursor: pointer;
}

.datepicker td.day:hover, .datepicker th:hover {
  background-color: #f3f4f6;
}

.datepicker td.today {
  background-color: #e0f2fe !important;
  color: #0284c7 !important;
  border-radius: 8px;
}

.datepicker td.active, .datepicker td.active:hover {
  background-color: #2563eb !important;
  color: #fff !important;
  font-weight: 600;
  border-radius: 8px;
}

.datepicker td.disabled,
.datepicker td.disabled:hover {
  color: #9ca3af !important;
  background: transparent !important;
  cursor: not-allowed !important;
}

.datepicker-switch, .datepicker .prev, .datepicker .next {
  color: #374151;
  font-weight: 500;
  transition: color 0.15s ease;
}

.datepicker-switch:hover, .datepicker .prev:hover, .datepicker .next:hover {
  color: #2563eb;
}

.datepicker .datepicker-switch {
  border-radius: 6px;
  padding: 6px 8px;
}

.datepicker tfoot tr th.today,
.datepicker tfoot tr th.clear {
  border-radius: 6px;
  font-size: 0.85rem;
}

.bootstrap-datepicker {
  z-index: 1200 !important; /* always above modal */
}

.dt-center {
    text-align: center;
    }
.dt-left {
    text-align: left;
}
.dt-right {
    text-align: right;
}
.wrap {
    white-space: normal !important;
}

.btn-secondary {
  color: #fff !important;
  background-color: #6c757d !important;
  border-color: #6c757d !important;
}

.btn-secondary:hover {
  color: #fff !important;
  background-color: #5c636a !important;
  border-color: #565e64 !important;
}

.btn-check:focus + .btn-secondary,
.btn-secondary:focus {
  color: #fff !important;
  background-color: #5c636a !important;
  border-color: #565e64 !important;
  box-shadow: 0 0 0 0.25rem rgba(130,138,145,0.5) !important;
}

.btn-check:checked + .btn-secondary,
.btn-check:active + .btn-secondary,
.btn-secondary:active,
.btn-secondary.active,
.show > .btn-secondary.dropdown-toggle {
  color: #fff !important;
  background-color: #565e64 !important;
  border-color: #51585e !important;
}

.btn-secondary:disabled,
.btn-secondary.disabled {
  color: #fff !important;
  background-color: #6c757d !important;
  border-color: #6c757d !important;
  opacity: 0.65;
}

.btn-light-secondary {
  color: #41464b !important;
  background-color: #e2e3e5 !important;
  border-color: #d3d4d5 !important;
}

.btn-light-secondary:hover {
  color: #000 !important;
  background-color: #d3d4d5 !important;
  border-color: #c6c7c8 !important;
}

.btn-check:focus + .btn-light-secondary,
.btn-light-secondary:focus {
  color: #000 !important;
  background-color: #d3d4d5 !important;
  border-color: #c6c7c8 !important;
  box-shadow: 0 0 0 0.25rem rgba(130,138,145,0.25) !important;
}

.btn-check:checked + .btn-light-secondary,
.btn-check:active + .btn-light-secondary,
.btn-light-secondary:active,
.btn-light-secondary.active,
.show > .btn-light-secondary.dropdown-toggle {
  color: #000 !important;
  background-color: #c6c7c8 !important;
  border-color: #babbbc !important;
}

.btn-light-secondary:disabled,
.btn-light-secondary.disabled {
  color: #6c757d !important;
  background-color: #e2e3e5 !important;
  border-color: #e2e3e5 !important;
  opacity: 0.65;
}

.dataTables_length label {
  display: flex;
  align-items: center;
  gap: .5rem;
  margin-bottom: 0;
}

.dataTables_length select {
  width: auto !important;
  min-width: 70px;
}

.custom-verti-nav-pills .nav-link.disabled {
  color: var(--bs-gray-500) !important;   /* gray text */
  background-color: var(--bs-light) !important;
  opacity: 0.65;
  cursor: not-allowed;
  pointer-events: none; /* prevent click */
  border-color: transparent !important;
}

.btn-custom-sm {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;          /* adjust size */
    height: 32px;
    padding: 0;            /* remove default padding */
    border-radius: 50%;    /* make it circular */
    font-size: 0.9rem;     /* icon size */
    line-height: 1;
}
.btn-custom-sm.btn-primary {
    background-color: var(--bs-primary);
    color: #fff;
}

.btn-custom-sm.btn-success {
    background-color: var(--bs-success);
    color: #fff;
}

.btn-custom-sm.btn-danger {
    background-color: var(--bs-danger);
    color: #fff;
}

/* Optional hover effect */
.btn-custom-sm:hover {
    opacity: 0.9;
    transform: scale(1.05);
    transition: all 0.2s ease;
}
.form-control-custom-sm {
    height: 32px; /* consistent height */
    padding: 0.25rem 0.5rem;
    font-size: 0.8125rem; /* ~13px */
    line-height: 1.5;
    border-radius: 0.4rem;
    border: 1px solid var(--bs-border-color);
    background-color: var(--bs-body-bg);
    color: var(--bs-body-color);
    transition: all 0.2s ease-in-out;
}

/* Focus effect - matches Morvin theme */
.form-control-custom-sm:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.15rem rgba(var(--bs-primary-rgb), 0.25);
    outline: none;
}

/* Disabled or read-only */
.form-control-custom-sm:disabled,
.form-control-custom-sm[readonly] {
    background-color: var(--bs-secondary-bg);
    opacity: 0.8;
}

.select2-container--default .select2-selection--single.form-control-custom-sm {
    height: 32px !important;
    border: 1px solid var(--bs-border-color);
    border-radius: 0.4rem;
    font-size: 0.8125rem;
    background-color: var(--bs-body-bg);
    display: flex !important;
    align-items: center !important;
    padding: 0 0.5rem !important;
    
}

/* remove built-in line-height offset */
.select2-container--default .select2-selection--single.form-control-custom-sm .select2-selection__rendered {
    padding-left: 0 !important;
    padding-right: 0 !important;
    line-height: normal !important;
    display: block !important;
    position: relative !important;
    top: 50% !important;
    transform: translateY(-50%) !important; /* <-- this centers perfectly */
    font-size: 0.8125rem;
    color: var(--bs-body-color);
}

/* fix arrow alignment */
.select2-container--default .select2-selection--single.form-control-custom-sm .select2-selection__arrow {
    height: 100% !important;
    top: 0 !important;
    /* right: 6px !important; */
    display: flex !important;
    align-items: center !important;
}

input[readonly],
textarea[readonly],
select[readonly],
.form-control[readonly],
.form-select[readonly] {
    background-color: #f2f3f5 !important;
    color: #6c757d !important;
    border-color: #d9d9d9 !important;
    cursor: not-allowed !important;
    opacity: 1 !important;
    pointer-events: none !important;
}

/* File input (readonly still looks active by default) */
input[type="file"][readonly] {
    background-color: #f2f3f5 !important;
}

/* Select2 readonly (single select) */
.select2-container--default.select2-container--disabled .select2-selection--single {
    background-color: #f2f3f5 !important;
    border-color: #d9d9d9 !important;
    cursor: not-allowed !important;
    color: #6c757d !important;
}

/* Select2 readonly (multiple select) */
.select2-container--default.select2-container--disabled .select2-selection--multiple {
    background-color: #f2f3f5 !important;
    border-color: #d9d9d9 !important;
    cursor: not-allowed !important;
    color: #6c757d !important;
}

/* Datepicker readonly fix */
input[readonly].datepicker,
input[readonly].hasDatepicker {
    pointer-events: none !important;
    background-color: #f2f3f5 !important;
}

/* Optional: disable hover/focus effect */
input[readonly]:focus,
textarea[readonly]:focus,
select[readonly]:focus {
    box-shadow: none !important;
    outline: none !important;
} 


</style>

