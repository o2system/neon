<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <nav class="navbar navbar-expand-lg navbar-light account-cover-navbar bg-white p-relative">
                    <button type="button" class="btn btn-default navbar-toggler navbar-toggler-right" data-toggle="collapse" data-target="#navbar-22" aria-controls="navbar-22" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbar-22">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item active">
                                <a href="{{base_url('administrator/settings/general')}}" class="nav-link">General</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{base_url('administrator/settings/writing')}}" class="nav-link">Writing</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{base_url('administrator/settings/discussion')}}" class="nav-link">Discussion</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{base_url('administrator/settings/traffic')}}" class="nav-link">Traffic</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {{$page->content}}
            </div>
        </div>
    </div>
</div>