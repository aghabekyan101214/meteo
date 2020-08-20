@extends("layouts.auth")
@section("content")
    <section id="wrapper" class="new-login-register">
        <div class="lg-info-panel">
            <div class="inner-panel">
                <div class="lg-content">
                    <h2>Բարի Գալուստ Մետեո</h2>
                </div>
            </div>
        </div>
        <div class="new-login-box">
            <div class="white-box">
                <h3 class="box-title m-b-0">Մուտք Դեպի Համակարգ</h3>
                <small>Լրացրեք Ձեր տվյալները ներքևում</small>
                <form class="form-horizontal new-lg-form" id="loginform" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group  m-t-20">
                        <div class="col-xs-12">
                            <label>Էլ․ Հասցե</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                            <span class="text-danger" role="alert"> <strong>{{ $message }}</strong> </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12">
                            <label>Գաղտնաբառ</label>
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror" name="password" required
                                   autocomplete="current-password">
                            @error('password')
                            <span class="text-danger" role="alert"> <strong>{{ $message }}</strong> </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button
                                class="btn btn-info btn-lg btn-block btn-rounded text-uppercase waves-effect waves-light" type="submit">Մուտք
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </section>
@endsection
