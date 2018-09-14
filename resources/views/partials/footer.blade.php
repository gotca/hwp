<footer>
    <section class="footer-section follow-us bg--accent bg--inner-shadow">
        <div class="bg-elements bg--grid blend--overlay"></div>

        <div class="container text-align--center">
            <h1>Follow us <a href="https://twitter.com/intent/follow?screen_name=HHSWaterPolo">@HHSWaterPolo</a> for live scoring updates</h1>
        </div>
    </section>

    <section class="footer-section main bg--primary">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <section class="twitter-embed">
                        <a
                            class="twitter-timeline"
                            href="https://twitter.com/HHSWaterPolo"
                            data-widget-id="384422471509618689"
                            data-link-color="#6ba3d0"
                            data-chrome="noheader nofooter noborders transparent"
                            data-tweet-limit="1"
                            data-theme="dark"
                        >Tweets by @HHSWaterPolo</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                    </section>
                </div>
                <div class="col-xs-12 col-md-8">
                    @include('partials.playerlist')
                </div>
            </div>
        </div>
    </section>

    <section class="footer-section sub text-align--center">
        <p>Special thanks to Mrs. Grandy and Mrs. Tuttle for taking great pictures &amp; <a href="{{route('players', ['nameKey' => 'EzraDejonge'])}}" title="Ezra Dejonge">Ezra Dejonge</a> for making the sweet 3d polo ball!</p>
    </section>

</footer>