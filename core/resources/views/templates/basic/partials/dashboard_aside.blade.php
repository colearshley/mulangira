<aside class="dashboard__sidebar">
    <div class="dashboard__logo">
        <span class="close-dashboard-sidebar d-lg-none">
            <i class="las la-times"></i>
        </span>
    </div>
    <div class="side__menu__area">
        <div class="side__menu__area-inner">
            <div class="dashboard__author">
                <div class="thumb">
                    <a href="{{ route('user.home') }}">
                        <img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, getFileSize('userProfile')) }}" alt="user">
                    </a>
                </div>
                <div class="content">
                    <h6 class="title">
                        <a href="{{ route('user.home') }}" class="text--base">{{ auth()->user()->fullname }}</a>
                    </h6>
                    <a href="{{ route('user.home') }}" class="text--base fz--14">@lang('@'){{ auth()->user()->username }}</a>
                </div>
            </div>

            <ul class="side__menu">
                <li>
                    <a class="{{ menuActive('user.home') }}" href="{{ route('user.home') }}">
                        <i class="las la-home"></i> <span class="cont">@lang('Dashboard')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.order.index') }}" href="{{ route('user.order.index') }}">
                        <i class="las la-list-ul"></i> <span class="cont">@lang('My Orders')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.review*') }}" href="{{ route('user.review.index') }}">
                        <i class="las la-haykal"></i> <span class="cont">@lang('Review Products')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.transactions') }}" href="{{ route('user.transactions') }}">
                        <i class="las la-money-bill-wave"></i> <span class="cont">@lang('Transactions History')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('ticket*') }}" href="{{ route('ticket.index') }}">
                        <i class="las la-money-check"></i> <span class="cont">@lang('Support Tickets')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.profile.setting') }}" href="{{ route('user.profile.setting') }}">
                        <i class="las la-user-tie"></i> <span class="cont">@lang('Profile')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.change.password') }}" href="{{ route('user.change.password') }}">
                        <i class="las la-key"></i> <span class="cont">@lang('Change Password')</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.logout') }}">
                        <i class="las la-sign-in-alt"></i> <span class="cont">@lang('Logout')</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
