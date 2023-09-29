<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AppPermissionController;
use App\Http\Controllers\Auth\AccountVerificationController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthorizationController;
use App\Http\Controllers\AuthorizationManualController;
use App\Http\Controllers\BackendEshopController;
use App\Http\Controllers\UsermanageController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CheckoutShipmentController;
use App\Http\Controllers\CheckstockController;
use App\Http\Controllers\DailySaleController;
use App\Http\Controllers\DailySaleCustomerGroupController;
use App\Http\Controllers\DailySaleTop10Controller;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EplatformController;
use App\Http\Controllers\EshopController;
use App\Http\Controllers\FixPermissionController;
use App\Http\Controllers\FixPermissionUserController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LazadaApiController;
use App\Http\Controllers\LeaveApproveController;
use App\Http\Controllers\LeaveApproveHRController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveManageController;
use App\Http\Controllers\RecordWorkingController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LeaveTypePropertyController;
use App\Http\Controllers\NocNocApiController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\LeaveRecordController;
use App\Http\Controllers\MonthlySaleController;
use App\Http\Controllers\MonthlySaleCustomerGroupController;
use App\Http\Controllers\NotificationManagementController;
use App\Http\Controllers\RequestLabelController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesAreaController;
use App\Http\Controllers\ShippingCompanyController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\ShopeeApiController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TikTokApiController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\RepairPrintController;
use App\Http\Controllers\RepairDashboardController;
use App\Http\Controllers\AutomotiveController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\SalesFormController;
use App\Http\Controllers\NotificationProductDiscountRepair;
use App\Http\Controllers\ProductDiscountController;
use App\Http\Controllers\ProductDecorateController;
use App\Http\Controllers\NotificationProductDecorate;
use App\Http\Controllers\SpecialDiscountController;
use App\Http\Controllers\NotificationSpecialDiscount;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockOdooController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\Middleware\ProductOnlineController as MiddlewareProductOnlineController;
use App\Http\Controllers\Middleware\OrdersController as MiddlewareOrdersController;
use App\Http\Controllers\Middleware\OrderReportController as MiddlewareOrderReportController;
use App\Http\Controllers\Middleware\FinancialController as MiddlewareFinancialController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

Route::group(['middleware' => ['prevent-back-history', 'prevent-maintenance']], function () {
    Auth::routes(['verify' => true]);
    Route::get('/', [DashboardController::class, 'home'])->name('home');
    Route::get('home', [DashboardController::class, 'home']);
    Route::get('get-holidays', [DashboardController::class, 'getHolidays']);
    Route::get('get-events', [DashboardController::class, 'getEvents']);
    Route::get('calendar/show', [CalendarController::class, 'showdata'])->name('show-event');

    Route::group(['middleware' => ['auth']], function () {
        Route::get('verify-account', [AccountVerificationController::class, 'verifyAccount']);
        Route::post('account-verified', [AccountVerificationController::class, 'accountVerified'])->name('account.verified');
    });
    Route::group(['middleware' => ['auth']], function () {
        Route::group(['middleware' => ['account_verified', 'user_active']], function () {

            Route::group(['prefix' => 'chatbot'], function () {
                Route::controller(ChatbotController::class)->group(function () {
                    // Route::get('/', 'index');
                    // Route::get('history', 'history');
                    // Route::post('query', 'query');

                    Route::get('/line_users', 'line_users');
                    Route::get('/line_search', 'line_search');
                    Route::post('/line_update_status_chatbot', 'line_update_status_chatbot');
                    Route::post('/line_update_status_chatbot_select', 'line_update_status_chatbot_select');
                });
            });

            Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
            Route::post('change-avatar', [ProfileController::class, 'changeAvatar'])->name('profile.change.avatar');
            Route::post('remove-avatar', [ProfileController::class, 'removeAvatar'])->name('profile.remove.avatar');
            Route::post('update-personal-data', [ProfileController::class, 'updatePersonalData'])->name('profile.update.personal-data');
            Route::post('change-password', [ChangePasswordController::class, 'changePassword'])->name('profile.change.password');
            Route::get('change-password-account', [ChangePasswordController::class, 'showChangePasswordAccount'])->name('account.change.password');

            Route::get('userdetail', [UsermanageController::class, 'userdetail'])->name('userdetail');

            Route::group(['prefix' => 'events'], function () {
                Route::controller(EventController::class)->group(function () {
                    Route::get('show/{id}', 'show');
                    Route::middleware(['manage_event'])->group(function () {
                        Route::get('/', 'index');
                        Route::get('q', 'search')->name('events.search');
                        Route::get('create', 'create');
                        Route::post('store', 'store')->name('events.store');
                        Route::get('edit/{id}', 'edit');
                        Route::get('del/{id}', 'destroy');
                        Route::post('update', 'update')->name('events.update');
                        Route::post('file-upload', 'file_upload')->name('events.file.upload');
                    });
                });
            });

            Route::group(['prefix' => 'manual'], function () {
                Route::controller(ManualController::class)->group(function () {
                    Route::get('/', 'index');
                    Route::get('manual-search', 'search')->name('manual.search');
                    Route::post('store', 'store')->name('manual.store');
                    Route::get('manual-del/{id}', 'destroy');
                });
            });

            Route::group(['prefix' => 'leave'], function () {
                Route::group(['middleware' => ['app_leave']], function () {
                    Route::controller(LeaveController::class)->group(function () {
                        Route::get('form', 'form');
                        Route::get('document/{id}', 'leave_document');
                        Route::get('document/pdf/{id}', 'leave_document_pdf');
                        Route::get('attach/{id}', 'attach_file');
                        Route::post('store', 'store')->name('leave.store');
                        Route::get('edit/{id}', 'edit');
                        Route::post('update', 'update')->name('leave.update');
                        Route::get('del/{id}', 'destroy');
                        Route::get('dashboard', 'dashboard');
                        Route::get('leave/search', 'leave_search');
                        Route::get('record-working/search', 'record_working_search');
                        Route::get('attendance-log/search', 'attendance_log_search');
                        Route::get('statistic-period/search', 'statistic_period_search');
                        Route::get('statistics/search', 'statistics_search');
                        Route::get('statistic-byperiod/search', 'statistic_byperiod_search');
                        Route::get('dash-change', 'dash_change');
                        Route::get('search-emp', 'search_emp');
                        Route::get('get-emp', 'get_emp');
                        Route::get('history', 'history');
                        Route::get('history/search', 'history_search');
                    });
                    Route::controller(RecordWorkingController::class)->group(function () {
                        Route::get('document-record-working/{id}', 'record_working_document');
                        Route::get('document-record-working/pdf/{id}', 'record_working_document_pdf');
                        Route::get('record-working-form', 'form');
                        Route::post('record-working-store', 'store')->name('record-working.store');
                        Route::get('record-working-edit/{id}', 'edit');
                        Route::post('record-working-update', 'update')->name('record-working.update');
                        Route::get('record-working-del/{id}', 'destroy');
                        Route::get('rw-history', 'record_working_history');
                        Route::get('rw-history/search', 'record_working_history_search');
                    });
                    Route::controller(LeaveRecordController::class)->group(function () {
                        Route::get('leave-record', 'index');
                        Route::get('leave-record/search', 'search');
                        Route::get('leave-record-form', 'form');
                        Route::get('leave-record-form/search', 'form_search');
                        Route::get('leave-record-view/id/{id}', 'view');
                        Route::get('leave-record-view/search', 'view_search');
                        Route::get('leave-record/download', 'download');
                        Route::post('leave-record-store', 'store')->name('leave-record.store');
                        Route::post('leave-record-destroy', 'destroy');
                    });
                    Route::group(['prefix' => 'approve'], function () {
                        Route::controller(LeaveApproveController::class)->group(function () {
                            Route::get('dashboard', 'dashboard');
                            Route::get('calendar', 'calendar');
                            Route::get('pending-search', 'pending_search');
                            Route::get('approved-search', 'approved_search');
                            Route::get('users-search', 'users_search');
                            Route::get('users-d-search', 'users_d_search');
                            Route::get('cancel-search', 'cancel_search');
                            Route::get('emp-attendance', 'emp_attendance');
                            Route::get('emp-attendance-search', 'emp_attendance_search');
                            Route::get('emp-attendance-log/id/{id}', 'emp_attendance_log');
                            Route::get('emp-attendance-log/search', 'emp_attendance_log_search');
                            Route::get('emp-leave-history', 'emp_leave_history');
                            Route::get('emp-leave-history/search', 'emp_leave_history_search');
                            Route::get('emp-leave-form/{id}', 'emp_leave_form');
                            Route::post('emp-leave-store', 'emp_leave_store')->name('approve.emp-leave-store');
                            Route::get('emp-leave-edit/{id}', 'emp_leave_edit');
                            Route::post('emp-leave-update', 'emp_leave_update')->name('approve.emp-leave-update');
                            Route::get('emp-leave-approve/{id}', 'emp_leave_approve');
                            Route::post('emp-leave-approved', 'emp_leave_approved')->name('approve.emp-leave-approved');
                            Route::post('leave-approve/submit', 'leave_approve_submit');
                            Route::post('emp-leave-cancel', 'emp_leave_cancel');
                            Route::get('search-emp', 'search_emp');

                            Route::get('record-working-pending-search', 'record_working_pending_search');
                            Route::get('record-working-approved-search', 'record_working_approved_search');
                            Route::get('emp-record-working-history', 'emp_record_working_history');
                            Route::get('emp-record-working-history/search', 'emp_record_working_history_search');
                            Route::post('record-working-approve/submit', 'record_working_approve_submit');
                            Route::get('emp-record-working-approve/{id}', 'emp_record_working_approve');
                            Route::post('emp-record-working-approved', 'emp_record_working_approved')->name('approve.emp-record-working-approved');
                            Route::post('emp-record-working-cancel', 'emp_record_working_cancel');
                        });
                    });
                    Route::group(['middleware' => ['manage_employee']], function () {
                        Route::group(['prefix' => 'approve-hr'], function () {
                            Route::controller(LeaveApproveHRController::class)->group(function () {
                                Route::get('dashboard', 'dashboard');
                                Route::get('calendar', 'calendar');
                                Route::get('emp-leave-edit/{id}', 'emp_leave_edit');
                                Route::post('emp-leave-update', 'emp_leave_update')->name('approve-hr.emp-leave-update');
                                Route::post('emp-leave-return', 'emp_leave_return');
                                Route::post('emp-leave-cancel', 'emp_leave_cancel');
                                Route::get('leave-approve', 'leave_approve');
                                Route::get('leave-approve/search', 'search_leave_approve');
                                Route::post('leave-approve/submit', 'leave_approve_submit');
                                Route::get('search-emp', 'search_emp');

                                Route::get('record-working-approve', 'record_working_approve');
                                Route::get('record-working-approve/search', 'search_record_working_approve');
                                Route::post('record-working-approve/submit', 'record_working_approve_submit');
                                Route::post('emp-record-working-return', 'emp_record_working_return');
                                Route::post('emp-record-working-cancel', 'emp_record_working_cancel');
                            });
                        });
                        Route::group(['prefix' => 'manage'], function () {
                            Route::controller(LeaveManageController::class)->group(function () {
                                Route::get('search-emp', 'search_emp');
                                Route::get('allusers-search', 'allusers_search');
                                Route::get('emp-leave-history', 'emp_leave_history');
                                Route::get('emp-leave-history/search', 'emp_leave_history_search');
                                Route::get('emp-record-working-history', 'emp_record_working_history');
                                Route::get('emp-record-working-history/search', 'emp_record_working_history_search');
                                Route::get('emp-attendance', 'emp_attendance');
                                Route::get('emp-attendance-search', 'emp_attendance_search');
                                Route::get('emp-attendance-log/id/{id}', 'emp_attendance_log');
                                Route::get('emp-attendance-log/search', 'emp_attendance_log_search');
                                Route::get('period-salary', 'period_salary');
                                Route::get('period-salary/search', 'period_salary_search');
                                Route::get('period-salary/create', 'period_salary_create');
                                Route::post('period-salary/store', 'period_salary_store')->name('period-salary.store');
                                Route::get('period-salary/edit/{id}', 'period_salary_edit');
                                Route::post('period-salary/update', 'period_salary_update')->name('period-salary.update');
                                Route::get('period-salary/del/{id}', 'period_salary_destroy');
                                Route::get('upload', 'upload');
                                Route::post('upload-file', 'upload_file');
                                Route::get('fingerprint', 'fingerprint');
                                Route::post('fingerprint-upload', 'fingerprint_upload');
                                Route::post('fingerprint/store', 'fingerprint_store')->name('fingerprint.store');
                                Route::post('fingerprint-data', 'fingerprint_data');
                                Route::post('fingerprint-data-download', 'fingerprint_data_download');
                                Route::get('fingerprint/download/{name}', 'fingerprint_download');
                            });
                            Route::controller(LeaveTypeController::class)->group(function () {
                                Route::get('leave-type', 'index');
                                Route::get('leave-type/search', 'search');
                                Route::get('leave-type/create', 'create');
                                Route::post('leave-type/store', 'store')->name('leave-type.store');
                                Route::get('leave-type/edit/{id}', 'edit');
                                Route::post('leave-type/update', 'update')->name('leave-type.update');
                                Route::get('leave-type/del/{id}', 'destroy');
                            });
                            Route::controller(LeaveTypePropertyController::class)->group(function () {
                                Route::get('leave-type-property', 'index');
                                Route::get('leave-type-property/search', 'search');
                                Route::get('leave-type-property/create', 'create');
                                Route::post('leave-type-property/store', 'store')->name('leave-type-property.store');
                                Route::get('leave-type-property/edit/{id}', 'edit');
                                Route::post('leave-type-property/update', 'update')->name('leave-type-property.update');
                                Route::get('leave-type-property/del/{id}', 'destroy');
                            });
                        });
                    });
                    Route::group(['prefix' => 'manage'], function () {
                        Route::controller(LeaveManageController::class)->group(function () {
                            Route::group(['prefix' => 'selection'], function () {
                                Route::get('period-salary', 'selection_period_salary');
                            });
                        });
                    });
                });
            });

            Route::group(['prefix' => 'sales-document'], function () {
                Route::controller(SalesFormController::class)->group(function () {
                    Route::get('sales-form', 'index');
                    Route::get('sales-form/create', 'create');
                    Route::get('sales-form/show/{id}', 'show');
                    Route::get('sales-form/edit/{id}', 'edit');
                    Route::get('sales-form/print/{id}', 'print');
                    Route::get('sales-form/cancel/{id}/{name}', 'cancel');
                    Route::post('sales-form/store', 'store')->name('sd.sales_form.store');
                    Route::get('sales-form/action', 'search')->name('sd.sales_form.search');
                    Route::get('sales-form/autocomplete-search', 'createAutocomplete')->name('sd.sales_form.Auto');
                });

                Route::controller(SalesFormController::class)->group(function () {
                    Route::get('sales-approve', 'indexApprove');
                    Route::get('sales-approve/action', 'searchWait')->name('sd.sales_approve.search');
                    Route::post('sales-approve/submit', 'submit')->name('sd.sales_approve.submit');
                    Route::get('sales-all/action', 'searchAll')->name('sd.sales_all.search');
                });

                Route::controller(SalesFormController::class)->group(function () {
                    Route::get('sales-list', 'indexList');
                    Route::get('sales-list/action', 'searchList')->name('sd.sales_list.search');
                    Route::get('sales-list/acknowledge', 'searchListAcknowledge')->name('sd.sales_list.searchAcknowledge');
                    Route::post('sales-list/submit', 'submitList')->name('sd.sales_list.submit');
                    Route::get('sales-list/print-log', 'printLog')->name('sd.sales_list.printLog');
                    Route::get('sales-list/show-print-log', 'showPrintLog')->name('sd.sales_list.showPrintLog');
                });

                Route::controller(ProductDiscountController::class)->group(function () {
                    Route::group(['prefix' => 'discount-mistake'], function () {
                        Route::group(['middleware' => ['marandsec']], function () {
                            Route::get('productdiscount-preview/{action}/{doc_id}/{status}', 'preView');
                            Route::get('productdiscount/print/{id}', 'print');
                            Route::get('productdiscount/file-download/{file}', 'downloadFile');
                            Route::post('log/print', 'logPrint')->name('discount.log.print');
                        });
                        Route::group(['middleware' => ['discountmar']], function () {
                            Route::get('clear/image', 'clearImage');
                            Route::post('removefile', 'removeFile')->name('removefile');
                            Route::post('uplod-image/request', [ProductDiscountController::class, 'imageRequest']);
                            Route::get('productdiscount-request-search', 'createAutoSearch')->name('request.search.Auto');
                            Route::get('productdiscount-request-searchName', 'createAutoSearchName')->name('request.search.AutoName');
                            Route::get('edit/product-discount-repair/{doc_id}/{id}/{status}', 'getEdit');
                            Route::post('delete/product-discount-repair', 'deleteRequest');
                            Route::post('update/product-discount-repair/{doc_id}/{id}/{status}', 'updateRequest')->name('update.Request');
                            Route::get('productdiscount-list_personal', 'index')->name('list.personal');
                            Route::get('productdiscount-request', 'request');
                            Route::post('productdiscount-request/create', 'request_create')->name('request_create');
                        });
                        Route::group(['middleware' => ['manager_approve']], function () {
                            Route::get('manager-approve/product-discount-repair', 'approveManager')->name('manger.approve');
                            Route::post('approve/product-discount-repair/manager-approve/{doc_id}/{id}/', 'previewManagerApprove')->name('admin.managerApprove');
                            Route::post('approve/product-discount-repair/manager-approve/', 'managerApprove');
                        });
                        Route::group(['middleware' => ['secretary_approve']], function () {
                            Route::get('secretary-approve/product-discount-repair', 'approveSecretary')->name('secretary.approve');
                            Route::post('approve/product-discount-repair/secretary-approve/', 'secretaryApprove');
                        });
                        Route::group(['middleware' => ['accounting']], function () {
                            Route::get('report', 'report')->name('discount.report');
                            Route::get('report-preview/{action}/{doc_id}/{status}', 'reportPreview');
                        });
                    });
                });

                Route::controller(ProductDecorateController::class)->group(function () {
                    Route::group(['prefix' => 'product-decorate'], function () {
                        Route::group(['middleware' => ['marandheadsec']], function () {
                            Route::get('preview/{action}/{doc_id}/{status}', 'preView');
                            Route::get('print/{id}', 'print');
                            Route::post('log/print', 'logPrint')->name('decorate.log.print');
                        });
                        Route::group(['middleware' => ['decoratemar']], function () {
                            Route::post('remove-image/request', 'removeImg')->name('decorate.remove.img');
                            Route::get('clear/image', 'clearImage');
                            Route::get('list-personal', 'index')->name('decorate.list.personal');
                            Route::get('request', 'request');
                            Route::post('request/create', 'createRequest')->name('decoratecreate.request');
                            Route::get('search/auto', 'autoSearch')->name('decorate.search.auto');
                            Route::post('uplod-image/request', 'imageRequest')->name('decorate.image.request');
                            Route::get('edit/{doc_id}/{id}/{status}', 'getEdit');
                            Route::post('update/{doc_id}/{id}/{status}', 'updateRequest')->name('decorate.update.Request');
                            Route::post('delete', 'deleteRequest');
                        });
                        Route::group(['middleware' => ['manager_approve']], function () {
                            Route::get('manager-approve', 'listManagerApprove')->name('decorate.manger.approve');
                            Route::post('approve/manager-approve/{doc_id}/{id}', 'previewManagerApprove')->name('decorate.managerApprove');
                            Route::post('approve/manager-approve/', 'managerApprove');
                        });
                        Route::group(['middleware' => ['headsecretary_approve']], function () {
                            Route::get('secretary-approve', 'listSecretaryApprove')->name('secretary.listapprove');
                            Route::post('approve/secretary-approve', 'secretaryApprove');
                        });
                        Route::group(['middleware' => ['accounting']], function () {
                            Route::get('report', 'report')->name('decorate.report');
                            Route::get('report-preview/{action}/{doc_id}/{status}', 'reportPreview');
                        });
                    });
                });

                Route::controller(SpecialDiscountController::class)->group(function () {
                    Route::group(['prefix' => 'special-discount'], function () {
                        Route::group(['middleware' => ['marandsec']], function () {
                            Route::get('preview/{action}/{doc_id}/{status}', 'preView');
                            Route::get('print/{id}', 'print');
                            Route::post('log/print', 'logPrint')->name('special.log.print');
                        });
                        Route::group(['middleware' => ['specialmar']], function () {
                            Route::get('list-personal', 'index')->name('special.list.personal');
                            Route::get('request', 'request');
                            Route::get('clear/image', 'clearImage');
                            Route::post('remove-image/request', 'removeImg')->name('special.remove.img');
                            Route::get('search/auto', 'autoSearch')->name('special.search.auto');
                            Route::post('uplod-image/request', 'imageRequest')->name('special.image.request');
                            Route::post('request/create', 'createRequest')->name('special.create.request');
                            Route::get('edit/{doc_id}/{id}/{status}', 'getEdit');
                            Route::post('update/{doc_id}/{id}/{status}', 'updateRequest')->name('special.update.Request');
                            Route::post('delete', 'deleteRequest');
                            Route::get('list-personal', 'index')->name('special.list.personal');
                        });
                        Route::group(['middleware' => ['manager_approve']], function () {
                            Route::get('manager-approve', 'listManagerApprove')->name('special.manger.approve');
                            Route::post('approve/manager-approve/{doc_id}/{id}', 'previewManagerApprove')->name('special.managerApprove');
                        });
                        Route::group(['middleware' => ['secretary_approve']], function () {
                            Route::get('secretary-approve', 'listSecretaryApprove')->name('secretary.listapprove');
                            Route::post('approve/secretary-approve', 'secretaryApprove');
                        });
                        Route::group(['middleware' => ['accounting']], function () {
                            Route::get('report', 'report')->name('special.report');
                            Route::get('report-preview/{action}/{doc_id}/{status}', 'reportPreview');
                        });
                    });
                });
            });

            Route::group(['prefix' => 'sales-report'], function () {
                Route::group(['middleware' => ['app_sale_report']], function () {
                    Route::controller(DailySaleController::class)->group(function () {
                        Route::get('daily-sales', 'index');
                        Route::get('daily-sales/action', 'search')->name('sr.daily_sales.search');
                        Route::get('daily-sales/summary', 'summary')->name('sr.daily_sales.summary');
                        Route::get('daily-sales/print', 'print');
                        Route::get('daily-sales/export', 'export');
                    });
                    Route::controller(DailySaleCustomerGroupController::class)->group(function () {
                        Route::get('daily-sales-customer', 'index');
                        Route::get('daily-sales-customer/action', 'search')->name('sr.daily_sales_customer.search');
                        Route::get('daily-sales-customer/print', 'print');
                        Route::get('daily-sales-customer/export', 'export');
                    });
                    Route::controller(DailySaleTop10Controller::class)->group(function () {
                        Route::get('daily-sales-top10', 'index');
                        Route::get('daily-sales-top10/action', 'search')->name('sr.daily_sales_top10.search');
                        Route::get('daily-sales-top10/print', 'print');
                        Route::get('daily-sales-top10/export', 'export');
                    });

                    Route::controller(MonthlySaleController::class)->group(function () {
                        Route::get('monthly-sales', 'index');
                        Route::get('monthly-sales/action', 'search')->name('sr.monthly_sales.search');
                        Route::get('monthly-sales/summary', 'summary')->name('sr.monthly_sales.summary');
                        Route::get('monthly-sales/print', 'print');
                        Route::get('monthly-sales/export', 'export');
                    });
                    Route::controller(MonthlySaleCustomerGroupController::class)->group(function () {
                        Route::get('monthly-sales-customer', 'index');
                        Route::get('monthly-sales-customer/action', 'search')->name('sr.monthly_sales_customer.search');
                        Route::get('monthly-sales-customer/print', 'print');
                        Route::get('monthly-sales-customer/export', 'export');
                    });
                });
            });

            Route::group(['prefix' => 'product'], function () {
                Route::controller(ProductController::class)->group(function () {
                    Route::get('search', 'index');
                    Route::get('search/action', 'product_search')->name('pd.search');
                    Route::get('search2', 'index2');
                    Route::get('search2/action', 'product_search2')->name('pd.search2');
                });
                Route::controller(ProductCategoryController::class)->group(function () {
                    Route::get('category-search', 'index');
                    Route::get('category-search/action', 'product_search')->name('pd.category.search');
                    Route::get('category-search2', 'index2');
                    Route::get('category-search2/action', 'product_search2')->name('pd.category.search2');
                    Route::get('category-file', function () {
                        return view('product.category-upload')->with('data', []);
                    });
                    Route::post('category-upload', 'upload')->name('pd.category.upload');
                    Route::post('category-update', 'update')->name('pd.category.update');
                    Route::post('category-edit-update', 'category_edit_update');
                    Route::get('stkcod-edit/search', 'stkcod_edit_search');
                    Route::get('sale-category/search', 'sale_category_search');
                    Route::get('main-category/search', 'main_category_search');
                    Route::get('sec-category/search', 'sec_category_search');
                    Route::get('online-category/search', 'online_category_search');
                    Route::get('daily-category/search', 'daily_category_search');
                    Route::get('model/search', 'model_search');
                    Route::get('color_code/search', 'color_code_search');
                    Route::get('size/search', 'size_search');
                });
                Route::group(['middleware' => ['app_generate_barcode']], function () {
                    Route::controller(BarcodeController::class)->group(function () {
                        Route::get('barcode-view', 'view');
                        Route::get('barcode-upload', 'upload');
                        Route::post('barcode-upload-print', 'upload_print')->name('barcode.upload-print');
                        Route::post('barcode-upload-generate', 'upload_generate')->name('barcode.upload-generate');
                        Route::get('barcode-new', function () {
                            return view('product.barcode-generate')->with('data', []);
                        });
                        Route::post('barcode-generate', 'generate')->name('barcode.generate');
                        Route::get('barcode-list', function () {
                            return view('product.barcode-list');
                        });
                        Route::get('barcode-edit/{id}', 'barcode_edit');
                        Route::get('barcode-cancel/{id}', 'barcode_cancel');
                        Route::get('barcode-search/action', 'barcode_search')->name('barcode.search');
                        Route::get('barcode-export/{id}', 'barcode_export')->name('barcode.export');
                        Route::post('barcode-action', 'barcode_action')->name('barcode.action');
                    });
                });
                Route::controller(RequestLabelController::class)->group(function () {
                    Route::group(['prefix' => 'request-label'], function () {
                        Route::get('/', 'index');
                        Route::get('q', 'search')->name('request-label.search');
                        Route::get('create', 'create');
                        Route::get('search-sku', 'search_sku');
                        Route::get('show/{id}', 'show');
                        Route::get('show-search', 'show_search');
                        Route::get('edit/{id}', 'edit');
                        Route::get('del/{id}', 'destroy');
                        Route::get('download/{id}', 'download');
                        Route::post('print-label', 'print_label');
                        Route::get('print_status/{id}', 'print_status');
                        Route::get('get_data', 'get_data')->name('request-label.get_data');
                        Route::get('add_data', 'add_data')->name('request-label.add_data');
                        Route::get('reset_data', 'reset_data')->name('request-label.reset_data');
                        Route::get('remove_data', 'remove_data')->name('request-label.remove_data');
                        Route::get('edit_qty', 'edit_qty')->name('request-label.edit_qty');
                        Route::post('store', 'store')->name('request-label.store');
                        Route::post('update', 'update')->name('request-label.update');
                        Route::post('upload', 'upload')->name('request-label.upload');
                        Route::get('download-template', 'download_template');
                    });
                });
                Route::group(['prefix' => 'stock'], function () {
                    Route::controller(StockController::class)->group(function () {
                        Route::get('/', 'index');
                        Route::get('/search/stock', 'search')->name('stock.search');
                    });
                });
                Route::group(['prefix' => 'odoo'], function () {
                    Route::controller(StockOdooController::class)->group(function () {
                        Route::get('import/stocks', 'index');
                        Route::post('upload', 'upload')->name('od.stock.uplaod');
                        Route::get('stocks', 'stocks');
                        Route::get('search', 'search')->name('od.stock.search');
                        Route::get('noneex', 'none_ex')->name('od.none.ex');
                        Route::get('none/stocks', 'noneStock');
                        Route::get('search-nonestocks', 'searchNoneStock')->name('od.searchNoneStock');
                    });
                });
            });

            Route::group(['prefix' => 'orders'], function () {
                Route::controller(OrdersController::class)->group(function () {
                    Route::get('download', 'downloadForm')->name('orders.download');
                    Route::post('search', 'search')->name('orders.search');
                    Route::get('export', 'export')->name('orders.export');
                    Route::get('form', 'form_orders');
                    Route::get('get', 'get_orders')->name('orders.get');
                    Route::get('data', 'get_data')->name('orders.data');
                });
            });

            Route::group(['prefix' => 'backend-eshop'], function () {
                Route::controller(BackendEshopController::class)->group(function () {
                    Route::get('image-products', 'image_products');
                    Route::get('get-image-products', 'get_image_products');
                    Route::get('download-products', 'download_products');
                    Route::get('get-products', 'get_products');
                });
            });

            Route::group(['prefix' => 'middleware'], function () {
                Route::group(['prefix' => 'product-online'], function () {
                    Route::controller(MiddlewareProductOnlineController::class)->group(function () {
                        Route::get('/', 'index');
                        Route::get('search', 'search');
                        Route::get('sku-edit/search', 'sku_edit_search');
                        Route::get('chanel/search', 'chanel_search');
                        Route::get('category/search', 'category_search');
                        Route::post('category-edit-update', 'category_edit_update');
                        Route::post('category-edit-delete', 'category_edit_delete');
                        Route::get('category-file', function () {
                            return view('middleware.product-online-upload')->with('data', []);
                        });
                        Route::post('category-upload', 'upload')->name('pdonline.category.upload');
                        Route::post('category-update', 'update')->name('pdonline.category.update');
                    });
                });
                Route::group(['prefix' => 'orders'], function () {
                    Route::controller(MiddlewareOrdersController::class)->group(function () {
                        Route::get('/', 'index');
                        Route::get('search', 'search')->name('middleware.order-list.get-data');
                        Route::post('upexport', 'updateAndExport');
                        Route::post('destroy', 'destroy');
                        Route::get('downloadfile/{file}', 'downloadFile');
                        Route::get('filehistory', 'fileHistory');
                        Route::get('show/{id}', 'show');
                        Route::get('load', 'loadOrders');
                        Route::get('get-orders', 'getOrders')->name('middleware.order-list.get-orders');
                    });
                    Route::group(['prefix' => 'report'], function () {
                        Route::controller(MiddlewareOrderReportController::class)->group(function () {
                            Route::get('/', 'index');
                            Route::get('get', 'get_orders')->name('middleware.order-report.get-orders');
                            Route::get('data', 'get_data')->name('middleware.order-report.get-data');
                            Route::get('export', 'export')->name('middleware.order-report.export');
                        });
                    });
                });
                Route::group(['prefix' => 'financial'], function () {
                    Route::controller(MiddlewareFinancialController::class)->group(function () {
                        Route::get('transaction', 'transaction');
                        Route::get('transaction/get', 'get_transaction');
                        Route::get('transaction/download', 'download_transaction');
                    });
                });
            });

            Route::group(['prefix' => 'shipping'], function () {
                Route::group(['middleware' => ['app_shipping']], function () {
                    Route::controller(ShippingController::class)->group(function () {
                        Route::get('print', 'shippingForm');
                        Route::get('search', 'search')->name('shipping.search');
                        Route::get('history', 'index');
                        Route::get('search-history', 'search_history')->name('shipping.search_history');
                        Route::get('print-history-log', 'history_print_log');
                        Route::get('trackingnumber/{trackingnumber}', 'viewPDF');
                        Route::get('print-history-clear/{trackingnumber}', 'print_history_clear');
                    });
                });
            });

            Route::group(['prefix' => 'checkout'], function () {
                Route::group(['middleware' => ['app_shipping_checkout']], function () {
                    Route::controller(CheckoutShipmentController::class)->group(function () {
                        Route::get('shipment', 'shipmentForm');
                        Route::get('shipment-detail/{id}', 'shipmentDetail');
                        Route::get('shipment-print/{id}', 'shipmentPrint');
                        Route::get('shipment2-print/{id}', 'shipment2Print');
                        Route::post('shipment-submit', 'shipmentSubmit')->name('checkout-shipment.submit');
                        Route::post('shipment-update', 'shipmentUpdate')->name('checkout-shipment.update');
                        Route::post('shipment-signature', 'shipmentSignature')->name('checkout-shipment.signature');
                        Route::get('shipment-del/{id}', 'shipmentDelete');
                        Route::get('shipment-del-item/{id}', 'shipmentItemDelete');
                        Route::get('shipment-tracking/{id}', 'shipmentTracking');
                        Route::get('shipment-data', 'getShipment')->name('checkout-shipment.data');
                        Route::get('search-shipment', 'search')->name('checkout-shipment.search');
                        Route::get('shipment-history', 'history');
                        Route::get('search-shipment-history', 'search_history')->name('checkout-shipment.search_history');
                    });
                    Route::controller(ShippingCompanyController::class)->group(function () {
                        Route::get('ship-com-manage', 'index');
                        Route::get('ship-com-add', 'add');
                        Route::post('ship-com-create', 'create')->name('ship-com-create');
                        Route::get('ship-com-edit/{id}', 'edit');
                        Route::put('ship-com-update/{id}', 'update');
                    });
                });
            });

            Route::group(['prefix' => 'organization'], function () {
                Route::group(['middleware' => ['app_organization']], function () {
                    Route::controller(DepartmentController::class)->group(function () {
                        Route::group(['prefix' => 'department'], function () {
                            Route::get('/', 'index');
                            Route::get('q', 'search')->name('department.search');
                            Route::get('export', 'export');
                            Route::get('site-map/{id}', 'site_map');
                            Route::group(['middleware' => ['manage_employee']], function () {
                                Route::get('create', 'create');
                                Route::get('show/{id}', 'show');
                                Route::get('edit/{id}', 'edit');
                                Route::get('del/{id}', 'destroy');
                                Route::post('store', 'store')->name('department.store');
                                Route::post('update', 'update')->name('department.update');
                                Route::get('get_dept_parent/{level}', 'get_dept_parent');
                            });
                        });
                        Route::get('/organizational-chart', 'organizational_chart');
                        Route::get('/organizational-chart/export', 'organizational_chart_export');
                    });
                    Route::controller(PositionController::class)->group(function () {
                        Route::group(['prefix' => 'position'], function () {
                            Route::get('/', 'index');
                            Route::get('q', 'search')->name('position.search');
                            Route::group(['middleware' => ['manage_employee']], function () {
                                Route::get('create', 'create');
                                Route::get('show/{id}', 'show');
                                Route::get('edit/{id}', 'edit');
                                Route::get('del/{id}', 'destroy');
                                Route::post('store', 'store')->name('position.store');
                                Route::post('update', 'update')->name('position.update');
                                Route::get('export', 'export');
                            });
                        });
                    });
                    Route::controller(SalesAreaController::class)->group(function () {
                        Route::group(['prefix' => 'sales-area'], function () {
                            Route::get('/', 'index');
                            Route::get('q', 'search')->name('sales-area.search');
                            Route::group(['middleware' => ['manage_employee']], function () {
                                Route::get('create', 'create');
                                Route::get('edit/{id}', 'edit');
                                Route::get('del/{id}', 'destroy');
                                Route::post('store', 'store')->name('sales-area.store');
                                Route::post('update', 'update')->name('sales-area.update');
                            });
                        });
                    });
                    Route::controller(EmployeeController::class)->group(function () {
                        Route::group(['prefix' => 'employees'], function () {
                            Route::get('/', 'index');
                            Route::get('search-level1', 'search_level1')->name('employee.level1');
                            Route::get('search-level2', 'search_level2')->name('employee.level2');
                            Route::get('search-level3', 'search_level3')->name('employee.level3');
                            Route::get('search-level4', 'search_level4')->name('employee.level4');
                            Route::get('search-sales-area', 'search_sales_area')->name('employee.sales-area');
                            Route::get('q', 'search')->name('employee.search');
                            Route::get('search-emp', 'search_emp');
                            Route::get('get-emp', 'get_emp');
                            Route::group(['middleware' => ['manage_employee']], function () {
                                Route::get('add', 'add');
                                Route::get('show/{id}', 'show');
                                Route::get('edit/{id}', 'edit');
                                Route::get('del/{id}', 'destroy');
                                Route::get('create', 'create');
                                Route::post('store', 'store')->name('employee.store');
                                Route::post('update', 'update')->name('employee.update');
                                Route::get('export-edit', 'exportEdit');
                                Route::get('upload', 'upload');
                                Route::post('upload-data', 'uploadData')->name('employee.upload-data');
                                Route::post('update-data', 'updateData')->name('employee.update-data');
                            });
                            Route::get('export', 'export');
                        });
                    });
                    Route::group(['middleware' => ['manage_employee']], function () {
                        Route::controller(AuthorizationController::class)->group(function () {
                            Route::group(['prefix' => 'authorization'], function () {
                                Route::get('/', 'index');
                                Route::get('search', 'search');
                                Route::get('create', 'create');
                                Route::post('store', 'store')->name('authorization.store');
                                Route::get('edit/{id}', 'edit');
                                Route::post('update', 'update')->name('authorization.update');
                                Route::get('del/{id}', 'destroy');
                            });
                        });
                        Route::controller(AuthorizationManualController::class)->group(function () {
                            Route::group(['prefix' => 'authorization-manual'], function () {
                                Route::get('/', 'index');
                                Route::get('search', 'search');
                                Route::get('create', 'create');
                                Route::post('store', 'store')->name('authorization-manual.store');
                                Route::get('edit/{id}', 'edit');
                                Route::post('update', 'update')->name('authorization-manual.update');
                                Route::get('del/{id}', 'destroy');
                            });
                        });
                    });
                });
            });

            Route::group(['prefix' => 'holidays'], function () {
                Route::controller(HolidayController::class)->group(function () {
                    Route::get('print', 'print');
                    Route::group(['middleware' => ['manage_employee']], function () {
                        Route::get('/', 'index');
                        Route::get('q', 'search')->name('holidays.search');
                        Route::get('create', 'create');
                        Route::post('store', 'store')->name('holidays.store');
                        Route::get('show/{id}', 'show');
                        Route::get('edit/{id}', 'edit');
                        Route::post('update', 'update')->name('holidays.update');
                        Route::get('del/{id}', 'destroy');
                    });
                });
            });

            Route::group(['prefix' => 'store'], function () {
                Route::controller(CheckstockController::class)->group(function () {
                    Route::get('checkstock', 'index')->name('checkstock');
                    Route::get('checkstock-data', 'data')->name('checkstock.data');
                    Route::get('checkstock-loaddata', 'loaddata')->name('checkstock.loaddata');
                    Route::get('checkstock-negative', 'negative')->name('checkstock.negative');
                    Route::get('checkstock-remove', 'remove')->name('checkstock.remove');
                    Route::get('checkstock-download/{file_name}', 'download')->name('checkstock.download');
                    Route::post('checkstock-reset', 'reset')->name('checkstock.reset');
                    Route::post('checkstock-save', 'save')->name('checkstock.save');
                });
            });

            Route::group(['prefix' => 'repair'], function () {
                Route::controller(RepairController::class)->group(function () {
                    Route::get('repair', 'index')->name('repair');
                    Route::get('approve', 'approve')->name('repair.approve');
                    Route::get('action', 'action')->name('repair.action');

                    Route::get('show/{id}', 'show');
                    Route::get('repair-form/{name}', 'form');
                    Route::get('repair-form-edit/{id}', 'form_edit');
                    Route::get('approve_form/{id}', 'approve_form');
                    Route::get('approve_form_edit/{id}', 'approve_form_edit');
                    Route::get('report_form/{id}', 'report_form');
                    Route::get('check_form/{id}', 'check_form');

                    Route::post('cancel', 'cancel');
                    Route::post('store', 'store')->name('repair.store');
                    Route::post('approve_update', 'approve_update')->name('repair.approve_update');
                    Route::post('work_update', 'work_update')->name('repair.work_update');
                    Route::post('report_update', 'report_update')->name('repair.report_update');
                    Route::post('check_update', 'check_update')->name('repair.check_update');
                });
                Route::controller(RepairController::class)->group(function () {
                    Route::get('q', 'search')->name('repair.search');
                    Route::get('qApprove', 'searchApprove')->name('repair.searchApprove');
                    Route::get('qManage', 'searchManage')->name('repair.searchManage');
                    Route::get('qCheck', 'searchCheck')->name('repair.searchCheck');
                    Route::get('autosearch', 'autoSearch')->name('repair.autoSearch');

                    Route::get('qa', 'searchOnce')->name('repair.searchOnce');
                    Route::get('qc', 'searchAction')->name('repair.searchAction');
                });

                Route::controller(RepairPrintController::class)->group(function () {
                    Route::get('detail-print/pdf/{id}', 'detail_print_pdf');
                });
                Route::controller(RepairDashboardController::class)->group(function () {
                    Route::get('dashboard/all', 'dashboardAll');
                    Route::get('dashboard/year', 'dashboardYear')->name('repair.dashboard.year');
                    Route::get('dashboard/dept', 'dashboardDept');
                    Route::get('dashboard/detail/{id}/{date}/{status}', 'dashboardEmp');
                    Route::get('dashboard/search-dept', 'searchDashboardDept')->name('repair.dashboard.searchDept');
                    Route::get('dashboard/search-emp', 'searchDashboardEmp')->name('repair.dashboard.searchEmp');
                });

                Route::controller(WithdrawController::class)->group(function () {
                    // Route::get('withdraw-list/{id}', 'indexNew');
                    Route::get('withdraw', 'index');
                    Route::get('withdraw-list/{id}', 'indexId')->name('withdraw.withdraw-list');
                    Route::get('withdraw-create/{id}', 'create');
                    Route::get('withdraw-edit/{id}', 'edit');
                    Route::get('withdraw-print-pdf/{id}', 'printPDF');
                    Route::get('search-withdraw', 'searchWithdraw')->name('withdraw.searchWithdraw');
                    Route::get('search-withdraw-all', 'searchWithdrawALL')->name('withdraw.searchWithdrawALL');
                    Route::post('withdraw-store', 'store')->name('withdraw.store');
                    Route::post('withdraw-update/', 'update')->name('withdraw.update');
                    Route::get('withdraw-delete/{id}', 'destroy');
                });
            });

            Route::group(['prefix' => 'automotive'], function () {
                Route::controller(AutomotiveController::class)->group(function () {
                    Route::get('automotive', 'index')->name('automotive');
                    Route::get('auto', 'search')->name('automotive.search');
                    Route::get('autocomplte', 'searchAuto')->name('automotive.searchAuto');
                    Route::get('create', 'create');
                    Route::get('show/{id}', 'show');
                    Route::get('edit/{id}', 'edit');
                    Route::get('del/{id}', 'destroy');
                    Route::get('get_model_parent/{id}', 'get_model_parent');
                    Route::post('store', 'store')->name('automotive.store');
                });

                Route::controller(AutomotiveController::class)->group(function () {
                    Route::get('main', 'main')->name('main');
                    Route::get('create', 'create');
                    Route::get('show/{id}', 'show');
                    Route::get('edit/{id}', 'edit');
                    Route::get('del/{id}', 'destroy');
                    Route::get('get_model_parent/{id}', 'get_model_parent');
                    Route::post('store', 'store')->name('automotive.store');

                    // page เพิ่มประเภท   
                    Route::get('add/{name}', 'add');
                    Route::get('edit-type/{id}/{name}', 'editType');
                    Route::get('delete/{id}/{name}', 'delete');
                    Route::post('store-type', 'storeType')->name('automotive.store-type');
                    Route::get('brand', 'searchBrand')->name('automotive.searchBrand');
                    Route::get('model', 'searchModel')->name('automotive.searchModel');
                    Route::get('types', 'searchTypes')->name('automotive.searchTypes');
                });
            });

            Route::group(['prefix' => 'admin'], function () {
                Route::group(['middleware' => ['admin']], function () {
                    Route::controller(AdminController::class)->group(function () {
                        Route::get('dashboard', 'index');
                        Route::get('users-active', 'users_active');
                    });

                    Route::group(['prefix' => 'application'], function () {
                        Route::controller(ApplicationController::class)->group(function () {
                            Route::get('/', 'index');
                            Route::get('add', 'add');
                            Route::post('store', 'store')->name('application.store');
                            Route::get('edit/{id}', 'edit');
                            Route::put('update', 'update')->name('application.update');
                            Route::get('del/{id}', 'destroy');
                        });

                        Route::controller(AppPermissionController::class)->group(function () {
                            Route::get('permission', 'index');
                            Route::get('permission/search', 'search');
                            Route::get('permission/get-users', 'get_users');
                            Route::get('permission/add-user', 'add_user');
                            Route::get('permission/remove-user', 'remove_user');
                            Route::get('permission/create', 'create');
                            Route::post('permission/store', 'store')->name('app-permission.store');
                            Route::get('permission/edit/{id}', 'edit');
                            Route::put('permission/update', 'update')->name('app-permission.update');
                            Route::get('permission/del/{id}', 'destroy');
                        });
                    });

                    Route::group(['prefix' => 'roles'], function () {
                        Route::controller(RoleController::class)->group(function () {
                            Route::get('/', 'index');
                            Route::get('search', 'search');
                            Route::get('create', 'create');
                            Route::post('store', 'store')->name('role.store');
                            Route::get('edit/{id}', 'edit');
                            Route::post('update', 'update')->name('role.update');
                            Route::get('del/{id}', 'destroy');
                        });
                    });

                    Route::group(['prefix' => 'permissions'], function () {
                        Route::controller(PermissionController::class)->group(function () {
                            Route::get('/', 'index');
                            Route::get('search', 'search');
                            Route::get('create', 'create');
                            Route::post('store', 'store')->name('permission.store');
                            Route::get('edit/{id}', 'edit');
                            Route::post('update', 'update')->name('permission.update');
                            Route::get('del/{id}', 'destroy');
                        });
                    });

                    Route::group(['prefix' => 'fix-permissions'], function () {
                        Route::controller(FixPermissionController::class)->group(function () {
                            Route::get('/', 'index');
                            Route::get('search', 'search');
                            Route::get('create', 'create');
                            Route::post('store', 'store')->name('fix-permission.store');
                            Route::get('edit/{id}', 'edit');
                            Route::post('update', 'update')->name('fix-permission.update');
                            Route::get('del/{id}', 'destroy');
                            Route::get('get-users', 'get_users');
                            Route::get('add-user', 'add_user');
                            Route::get('remove-user', 'remove_user');
                        });
                    });

                    Route::group(['prefix' => 'notifications'], function () {
                        Route::controller(NotificationManagementController::class)->group(function () {
                            Route::get('/', 'index');
                            Route::get('search', 'search');
                            Route::get('create', 'create');
                            Route::post('store', 'store')->name('notification.store');
                            Route::get('edit/{id}', 'edit');
                            Route::post('update', 'update')->name('notification.update');
                            Route::get('del/{id}', 'destroy');
                        });
                    });

                    Route::controller(UsermanageController::class)->group(function () {
                        Route::get('user-manage', 'index');
                        Route::get('q', 'search')->name('user-manage.search');
                        Route::get('register', 'register');
                        Route::post('createuser', 'createuser')->name('createuser');
                        Route::get('user-edit/{id}', 'edit');
                        Route::post('user-edit/{id}', 'updateuser')->name('edit.user');
                        Route::get('change-password/{id}', 'changepassword');
                        Route::post('change-password/{id}', 'store')->name('edit.password');
                        Route::get('reset/{id}', 'resetpassword');
                        Route::get('delete/{id}', 'delete');
                    });

                    Route::group(['prefix' => 'eplatform'], function () {
                        Route::controller(EplatformController::class)->group(function () {
                            Route::get('list', 'index');
                            Route::get('eplatform-add', 'add');
                            Route::post('eplatform-create', 'create')->name('eplatform.create');
                            Route::get('eplatform-edit/{id}', 'edit');
                            Route::put('eplatform-update/{id}', 'update');
                        });

                        Route::controller(EshopController::class)->group(function () {
                            Route::get('shop', 'index');
                            Route::get('eshop-add', 'add');
                            Route::post('eshop-create', 'create')->name('eshop.create');
                            Route::get('eshop-edit/{id}', 'edit');
                            Route::put('eshop-update/{id}', 'update');
                        });
                    });

                    Route::group(['prefix' => 'token'], function () {
                        Route::group(['prefix' => 'lazada'], function () {
                            Route::controller(LazadaApiController::class)->group(function () {
                                Route::get('/', 'index');
                                Route::get('access-token', 'access_token');
                                Route::post('generate-access-token', 'generate_access_token')->name('token.lazada.generate_access_token');
                                Route::get('check-token/{id}', 'check_token');
                                Route::get('refresh-token/{id}', 'refresh_token');
                                Route::put('refresh-access-token/{id}', 'refresh_access_token');
                            });
                        });
                        Route::group(['prefix' => 'shopee'], function () {
                            Route::controller(ShopeeApiController::class)->group(function () {
                                Route::get('/', 'index');
                                Route::get('access-token', 'access_token');
                                Route::post('generate-access-token', 'generate_access_token')->name('token.shopee.generate_access_token');
                                Route::get('check-token/{id}', 'check_token');
                                Route::get('refresh-token/{id}', 'refresh_token');
                                Route::put('refresh-access-token/{id}', 'refresh_access_token');
                            });
                        });
                        Route::group(['prefix' => 'nocnoc'], function () {
                            Route::controller(NocNocApiController::class)->group(function () {
                                Route::get('/', 'index');
                                Route::get('access-token', 'access_token');
                                Route::post('generate-access-token', 'generate_access_token')->name('token.nocnoc.generate_access_token');
                                Route::get('check-token/{id}', 'check_token');
                                // Route::get('refresh-token/{id}', 'refresh_token');
                                // Route::put('refresh-access-token/{id}', 'refresh_access_token');
                            });
                        });
                        Route::group(['prefix' => 'tiktok'], function () {
                            Route::controller(TikTokApiController::class)->group(function () {
                                Route::get('/', 'index');
                                Route::get('access-token', 'access_token');
                                Route::post('generate-access-token', 'generate_access_token')->name('token.tiktok.generate_access_token');
                                Route::get('check-token/{id}', 'check_token');
                                Route::get('refresh-token/{id}', 'refresh_token');
                                Route::put('refresh-access-token/{id}', 'refresh_access_token');
                            });
                        });
                    });

                    Route::group(['prefix' => 'test'], function () {
                        Route::controller(TestController::class)->group(function () {
                            Route::get('html5-qrcode', 'html5_qrcode');
                            Route::get('animation', 'animation');
                            Route::get('grid-elements', 'grid_elements');
                            Route::get('test_web_service_printer', 'test_web_service_printer');
                        });
                    });
                });
            });
        });
        Route::get('notification', [NotificationController::class, 'notifications'])->name('notification');
        Route::get('/remove-noti/{permission}/{app}/{doc_id}', [NotificationController::class, 'removeNoti']);
        Route::get('leftmenu-notification', [NotificationController::class, 'leftmenu_notifications'])->name('leftmenu-notification');
        Route::group(['prefix' => 'sales-document'], function () {
            Route::controller(NotificationProductDiscountRepair::class)->group(function () {
                Route::get('productdiscountrepair_noti', 'index')->name('pd.repair.noti');
            });
            Route::controller(NotificationProductDecorate::class)->group(function () {
                Route::get('productdecorate_noti', 'index')->name('decorate.noti');
            });
            Route::controller(NotificationSpecialDiscount::class)->group(function () {
                Route::get('specialdiscount_noti', 'index')->name('special.noti');
            });
        });
        // Route::get('logout', [LoginController::class, 'logout']);
    });
    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        return "Cache is cleared";
    });
    Route::get('test', [SpecialDiscountController::class, 'test']);
    Route::group(['prefix' => 'test'], function () {
        Route::controller(TestController::class)->group(function () {
            Route::get('receipt', 'receipt');
        });
    });
});
Route::get('spin', function () {
    return view('spin');
});
Route::fallback(function () {
    return view('errors.404');
});
Route::get('maintenance', function () {
    return view('errors.maintenance');
});
Route::get('lazada/callback', function () {
    return "Lazada Callback";
});
Route::get('shopee/callback', function () {
    return "Shopee Callback";
});
Route::get('nocnoc/callback', function () {
    return "NocNoc Callback";
});
Route::get('tiktok/callback', function () {
    return "TikTok Shop Callback";
});
Route::get('callback/{message}', function ($message) {
    alert()->warning($message);
    return redirect('/');
});
Route::get('callback-logout', function () {
    Auth::logout();
    Session::flush();
    return redirect('/');
});
