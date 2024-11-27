<x-backend.dashboard> 

 
    <!--begin::Row-->
    <div class="row g-5 g-xl-8">
        <!--begin::Col-->
        <div class="col-xl-4">
            <!--begin::Misc Widget 1-->
            <div class="mb-5 row mb-xl-8 g-5 g-xl-8">
                <!--begin::Col-->
                <div class="col-6">
                    <!--begin::Card-->
                    <a class="p-10 text-gray-800 card flex-column justfiy-content-start align-items-start text-start w-100 text-hover-primary" href="{{ route('account.profile') }}">
                        <i class="mb-5 text-gray-500 ki-duotone ki-gift fs-2tx ms-n1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                        <span class="fs-4 fw-bold">User Profile</span>
                    </a>
                    <!--end::Card-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-6">
                    <!--begin::Card-->
                    <a class="p-10 text-gray-800 card flex-column justfiy-content-start align-items-start text-start w-100 text-hover-primary" href="{{ route('account.activity') }}">
                        <i class="mb-5 text-gray-500 ki-duotone ki-technology-2 fs-2tx ms-n1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <span class="fs-4 fw-bold">Activity Log</span>
                    </a>
                    <!--end::Card-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-6">
                    <!--begin::Card-->
                    <a class="p-10 text-gray-800 card flex-column justfiy-content-start align-items-start text-start w-100 text-hover-primary" href="{{ route('account.security') }}">
                        <i class="mb-5 text-gray-500 ki-duotone ki-fingerprint-scanning fs-2tx ms-n1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                        <span class="fs-4 fw-bold">2FA Security</span>
                    </a>
                    <!--end::Card-->
                </div>
                <!--end::Col-->
                
                
                <!--begin::Col-->
                <div class="col-6">
                    <!--begin::Card-->
                    <a class="p-10 text-gray-800 card flex-column justfiy-content-start align-items-start text-start w-100 text-hover-primary" href="{{ route('college.portal') }}">
                        <i class="mb-5 text-gray-500 ki-duotone ki-rocket fs-2tx ms-n1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <span class="fs-4 fw-bold">College Portal</span>
                    </a>
                    <!--end::Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Misc Widget 1-->
            
        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-xl-8 ps-xl-12">
        <x-content.engage-widget />
            <!--begin::Row-->
            
            <!--end::Row-->
            <!--begin::Tables Widget 5-->
        
            <!--end::Tables Widget 5-->
            <!--begin::Row-->
            
            <!--end::Row-->
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->


</x-backend.dashboard>