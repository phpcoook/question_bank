<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('question.index')}}" class="brand-link">
        <img src="{{ url('assets/plugins/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
             class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">Quiz</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ url('assets/plugins/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                     alt="User Image">
            </div>
            <div class="info">
                @if(Auth::check())
                    <a href="{{route('question.index')}}" class="d-block">{{ Auth::user()->first_name }}</a>
                @endif
            </div>

        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @if(Auth::user()->role == 'admin')
                    <a href="{{ route('question.index') }}"
                       class="nav-link {{ request()->routeIs('question.index') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Question List</p>
                    </a>

                    <a href="{{ route('student.index') }}"
                       class="nav-link {{ request()->routeIs('student.index') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Student List</p>
                    </a>

                    <a href="{{ route('tutor.index') }}"
                       class="nav-link {{ request()->routeIs('tutor.index') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Tutor List</p>
                    </a>

                    <a href="{{ route('create.setting') }}"
                       class="nav-link {{ request()->routeIs('create.setting') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Setting</p>
                    </a>
                @endif

                    @if(Auth::user()->role == 'student')
                        <a href="{{ route('student.dashboard') }}"
                           class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Dashboard</p>
                        </a>
                        <a href="{{ route('student.start-quiz.addtime') }}"
                           class="nav-link {{ request()->routeIs('student.start-quiz.addtime') || request()->routeIs('student.start-quiz') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Start Quiz</p>
                        </a>
                    @endif

                    @if(Auth::user()->role == 'tutor')
                        <a href="{{ route('tutor.dashboard') }}"
                           class="nav-link {{ request()->routeIs('tutor.dashboard') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Dashboard</p>
                        </a>
                    @endif

                <li class="nav-item">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a href="#" class="nav-link"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
