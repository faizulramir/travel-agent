<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" key="t-menu">@lang('translation.Menu')
                    @if (auth()->user()->hasAnyRole('tra'))
                        <span key="t-ta">TRAVEL AGENT (TRA)</span>
                    @endif
                    @if (auth()->user()->hasAnyRole('ag'))
                        <span key="t-ta">DIY AGENT (AGN)</span>
                    @endif
                    @if (auth()->user()->hasAnyRole('fin'))
                        <span key="t-ta">AKC FINANCE (FIN)</span>
                    @endif
                    @if (auth()->user()->hasAnyRole('akc'))
                        <span key="t-ta">AKC ADMIN (AKC)</span>
                    @endif
                    @if (auth()->user()->hasAnyRole('ind'))
                        <span key="t-ta">DIY INDIVIDU (IND)</span>
                    @endif

                </li>

                <li>
                    <a href="{{ route('root') }}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dasboard">DASHBOARD</span>
                    </a>
                </li>

                @if (auth()->user()->hasAnyRole('tra'))
                    <li>
                        <a href="{{ route('excel_list') }}" class="waves-effect">
                            <i class="bx bx-file"></i>
                            <span key="t-dasboard">EXCEL LIST</span>
                        </a>
                    </li>
                @endif

                @if (auth()->user()->hasAnyRole('ag'))
                    <li>
                        <a href="{{ route('excel_list_agent') }}" class="waves-effect">
                            <i class="bx bx-file"></i>
                            <span key="t-ta">EXCEL LIST</span>
                        </a>
                    </li>
                @endif

                @if (auth()->user()->hasAnyRole('fin'))
                    <li>
                        <a href="{{ route('excel_list_finance') }}" class="waves-effect">
                            <i class="bx bx-file"></i>
                            <span key="t-ta">EXCEL LIST</span>
                        </a>
                    </li>
                @endif
                
                @if (auth()->user()->hasAnyRole('akc'))
                    <li>
                        <a href="{{ route('excel_list_admin') }}" class="waves-effect">
                            <i class="bx bx-file"></i>
                            <span key="t-ta">EXCEL LIST</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pcr_excel_list') }}" class="waves-effect">
                            <i class="bx bx-bolt-circle"></i>
                            <span key="t-ta">PCR LIST</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_list') }}" class="waves-effect">
                            <i class="bx bxs-user-detail"></i>
                            <span key="t-ta">USER LIST</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('plan_list') }}" class="waves-effect">
                            <i class="bx bxs-briefcase-alt-2"></i>
                            <span key="t-ta">PLAN LIST</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('setting_admin') }}" class="waves-effect">
                            <i class="bx bx-extension"></i>
                            <span key="t-dasboard">SETTINGS</span>
                        </a>
                    </li>
                @endif

                @if (auth()->user()->hasAnyRole('ind'))
                    <li>
                        <a href="{{ route('application_list') }}" class="waves-effect">
                            <i class="bx bx-message-square-edit"></i>
                            <span key="t-ta">APPLICATION</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
