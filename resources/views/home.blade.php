@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body text-center">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-10">
                                <input class="form-control form-control" placeholder="Type the list of github usernames you want to get (separate by commas)..." id="usernames">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary btn-block" id="fetch_data_btn">
                                    <i class="fab fa-github"></i>
                                    Fetch
                                </button>
                            </div>
                            <div class="col-md-12"><br>
                                <div class="form-group">
                                    <textarea class="form-control" style="height:400px;font:12px monospace" id="data_sample" readonly>Sample data will go here.</textarea>
                                </div>
                            </div>
                        </div>
                        
                    </div>

        
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">

    function fetchData(){
        var usernames = $("#usernames").val()
        $("#data_sample").val("Loading...");
        $.get(siteDir+'/git/fetch_user_data',{_token: token, usernames: usernames}).done(function(results){
            if(!results.status){
                alert("Failed to fetch data.");

            }else{
                alert("Successfully fetched data.");
                console.log(results.data);
            }

            $("#data_sample").val(JSON.stringify(results));

        });
    }

    $(document).ready(function(){

        $("#fetch_data_btn").on("click",function(event){
            fetchData();
        });

    });
</script>
@endsection