<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    data-scroll="false">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-white active" aria-current="page">Dashboard</li>
            </ol>
            <h6 class="font-weight-bolder text-white mb-0">Dashboard</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <div class="input-group">
                    <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                    <input type="text" class="form-control" placeholder="Type here...">
                </div>
            </div>
            <ul class="navbar-nav justify-content-end align-items-center">

                {{-- User Dropdown --}}
                <li class="nav-item dropdown d-flex align-items-center pe-2">

                    {{-- Trigger --}}
                    <a href="javascript:;" class="nav-link p-0" id="dropdownMenuButton"
                        aria-expanded="false">
                        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-pill"
                            style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-semibold"
                                style="width:34px; height:34px; font-size:13px;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="d-none d-sm-block lh-sm">
                                <div class="text-white fw-medium" style="font-size:13px;">{{ Auth::user()->name }}</div>
                                <div class="text-white opacity-50" style="font-size:10px;">
                                    {{ ucfirst(Auth::user()->role) }}</div>
                            </div>
                            <i class="fa fa-chevron-down text-white opacity-50 ms-1" style="font-size:9px;"></i>
                        </div>
                    </a>

                    {{-- Dropdown --}}
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2 p-0 overflow-hidden"
                        id="navbarUserDropdown"
                        style="width:250px; border-radius:16px;" aria-labelledby="dropdownMenuButton">

                        {{-- Header --}}
                        <li class="d-flex align-items-center gap-3 px-3 py-3 border-bottom">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-semibold flex-shrink-0"
                                style="width:42px; height:42px; font-size:16px;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-medium text-dark" style="font-size:14px;">{{ Auth::user()->name }}</div>
                                <div class="text-secondary" style="font-size:11px;">{{ Auth::user()->email }}</div>
                                <span class="badge rounded-pill mt-1"
                                    style="font-size:10px; background:#EEEDFE; color:#3C3489;">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            </div>
                        </li>

                        <li class="p-2">
                            <a href="#" class="dropdown-item d-flex align-items-center gap-2 rounded-3 py-2 px-2">
                                <div class="rounded-2 d-flex align-items-center justify-content-center bg-light text-secondary flex-shrink-0"
                                    style="width:30px; height:30px;">
                                    <i class="fas fa-user" style="font-size:12px;"></i>
                                </div>
                                <span style="font-size:13px;">Profile Saya</span>
                            </a>

                            <a href="#" class="dropdown-item d-flex align-items-center gap-2 rounded-3 py-2 px-2">
                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:30px; height:30px; background:#EEEDFE; color:#534AB7;">
                                    <i class="fas fa-cog" style="font-size:12px;"></i>
                                </div>
                                <span style="font-size:13px;">Pengaturan</span>
                            </a>

                            <hr class="my-1 opacity-10">

                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit"
                                    class="dropdown-item d-flex align-items-center gap-2 rounded-3 py-2 px-2 text-danger border-0 bg-transparent w-100">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width:30px; height:30px; background:#FCEBEB; color:#A32D2D;">
                                        <i class="fas fa-sign-out-alt" style="font-size:12px;"></i>
                                    </div>
                                    <span style="font-size:13px;">Logout</span>
                                </button>
                            </form>

                        </li>
                    </ul>
                </li>

                {{-- Sidenav Toggle (mobile) --}}
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line bg-white"></i>
                            <i class="sidenav-toggler-line bg-white"></i>
                            <i class="sidenav-toggler-line bg-white"></i>
                        </div>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>
