<x-partials.head />
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-fixed aside-secondary-disabled">
		<!--begin::Theme mode setup on page load-->
		<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
		<!--end::Theme mode setup on page load-->
		<!--begin::Main-->
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Page-->
			<div class="flex-row page d-flex flex-column-fluid">
				<x-app.sidebar />
				<!--begin::Wrapper-->
				<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
				<x-partials.header />
					<!--begin::Content-->
					<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
						<!--begin::Container-->
						<div class="container-xxl" id="kt_content_container">
							<!--begin::Row-->
							<div class="row g-5 g-xl-8">
								<!--begin::Col-->
								<div class="col-xl-4">
									<!--begin::Misc Widget 1-->
									<div class="mb-5 row mb-xl-8 g-5 g-xl-8">
										<!--begin::Col-->
										<div class="col-6">
											<!--begin::Card-->
											<a class="p-10 text-gray-800 card flex-column justfiy-content-start align-items-start text-start w-100 text-hover-primary" href="account/overview.html">
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
											<a class="p-10 text-gray-800 card flex-column justfiy-content-start align-items-start text-start w-100 text-hover-primary" href="account/statements.html">
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
											<a class="p-10 text-gray-800 card flex-column justfiy-content-start align-items-start text-start w-100 text-hover-primary" href="account/referrals.html">
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
						</div>
						<!--end::Container-->

						
						{{ $slot }}

					</div>
					<!--end::Content-->
					<x-partials.footer-content />
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::Root-->
		<!--begin::Drawers-->
	
		
		
		<!--end::Drawers-->
		<!--end::Main-->
		<!--begin::Scrolltop-->
		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
			<i class="ki-duotone ki-arrow-up">
				<span class="path1"></span>
				<span class="path2"></span>
			</i>
		</div>
		<!--end::Scrolltop-->
		<!--begin::Modals-->
		
		
		
		
		<!--end::Modals-->
<x-partials.footer />