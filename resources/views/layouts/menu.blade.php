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
{{--        <div class="user-panel mt-3 pb-3 mb-3 d-flex">--}}
{{--            <div class="image">--}}
{{--                <img src="{{ url('assets/plugins/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"--}}
{{--                     alt="User Image">--}}
{{--            </div>--}}
{{--            <div class="info">--}}
{{--                @if(Auth::check())--}}
{{--                    <a href="{{route('question.index')}}" class="d-block">{{ Auth::user()->first_name }}</a>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--        </div>--}}

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @if(Auth::user()->role == 'admin')

                    <a href="{{ route('topic.index') }}"
                       class="nav-link {{ request()->routeIs('topic.index') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Topic List</p>
                    </a>

                    <a href="{{ route('sub-topic.index') }}"
                       class="nav-link {{ request()->routeIs('sub-topic.index') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>SubTopic List</p>
                    </a>

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

                    <a href="{{ route('report') }}"
                       class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Reported Question</p>
                    </a>

                    <a href="{{ url('subscribers') }}"
                       class="nav-link {{ request()->is('subscribers') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Subscribers</p>
                    </a>
                    <a href="{{ url('payment_history') }}"
                       class="nav-link {{ request()->is('payment_history') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Payment History</p>
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

                        <a href="{{ route('student.wrong-question') }}"
                           class="nav-link {{ request()->routeIs('student.wrong-question') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Wrong Question</p>
                        </a>
                        <a href="{{ url('/student/previous-quiz') }}"
                           class="nav-link {{ request()->is('/student/previous-quiz') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Previous Quiz</p>
                        </a>
                        <a href="{{ url('payment_history') }}"
                           class="nav-link {{ request()->is('payment_history') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Payment History</p>
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
