<div class="dashboard__responsive__header d-flex align-items-center justify-content-between d-lg-none">
    <div class="thumb__wrapper d-flex align-items-center">
        <div class="thumb me-2">
            <img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, getFileSize('userProfile')) }}" alt="user">
        </div>
        <span class="username">@lang('@'){{ auth()->user()->username }}</span>
    </div>
    <div class="dashboard-sidebar-toggler"><i class="las la-sliders-h"></i></div>
</div>
