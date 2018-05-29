<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <form method="post">
                <div class="card-header d-flex align-items-center justify-content-between">
                    Related Posts
                    <div class="card-options">
                        <input type="submit" class="btn btn-primary btn-sm" name="saveRelated" value="Save Settings"/>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" <?php echo $traffic_show_related_post == 1 ? 'checked' : ''; ?> name="traffic_show_related_post" data-size="small" class="js-switch" data-color="#009efb" value="1"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Show related content after posts
                        </label>
                    </div>
                    <div class="form-group d-flex pl-5">
                        <div class="mt-0">
                            <input type="checkbox" <?php echo $traffic_show_related_header == 1 ? 'checked' : ''; ?> name="traffic_show_related_header" data-size="small" class="js-switch" data-color="#009efb" value="1"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Show a "Related" header to more clearly separate the related section from posts
                        </label>
                    </div>
                    <div class="form-group d-flex pl-5">
                        <div class="mt-0">
                            <input type="checkbox" <?php echo $traffic_striking_layout == 1 ? 'checked' : ''; ?> name="traffic_striking_layout" data-size="small" class="js-switch" data-color="#009efb" value="1"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Use a large and visually striking layout
                        </label>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <form method="post">
                <div class="card-header d-flex align-items-center justify-content-between">
                    Accelerated Mobile Pages (AMP)
                    <div class="card-options">
                        <input type="submit" class="btn btn-primary btn-sm" name="save" value="Save Settings"/>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" <?php echo $traffic_improve_mobile == 1 ? 'checked' : ''; ?> name="traffic_improve_mobile" data-size="small" class="js-switch" data-color="#009efb" value="1"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Improve the loading speed of your site on phones and tablets<br>
                            <span class="form-text text-muted">Your site supports the use of Accelerated Mobile Pages, a Google-led initiative that dramatically speeds up loading times on mobile devices.</span>
                        </label>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <form method="post">
                <div class="card-header">
                    Sitemaps
                </div>
                <div class="card-body">
                    <span class="form-text text-muted mb-2">Your sitemap is automatically sent to all major search engines for indexing.</span>
                    <a href="#">https://teguhrianto.com/sitemap.xml</a><br>
                    <a href="#">https://teguhrianto.com/news-sitemap.xml</a>
                </div>
            </form>
        </div>
        <div class="card">
            <form method="post">
                <div class="card-header d-flex align-items-center justify-content-between">
                    Site Verification Services
                    <div class="card-options">
                        <input type="submit" class="btn btn-primary btn-sm" name="saveVerification" value="Save Settings"/>
                    </div>
                </div>
                <div class="card-body">
                    <p>Note that <strong>verifying your site with these services is not necessary</strong> in order for your site to be indexed by search engines. To use these advanced search engine tools and verify your site with a service, paste the HTML Tag code below. Read the <a href="https://en.support.wordpress.com/webmaster-tools/">full instructions</a> if you are having trouble. Supported verification services: <a target="_blank" href="https://www.google.com/webmasters/tools/" class="external-link has-icon" rel="external noopener noreferrer">Google Search Console</a>, <a target="_blank" href="https://www.bing.com/webmaster/" class="external-link has-icon" rel="external noopener noreferrer">Bing Webmaster Center</a>, <a target="_blank" href="https://pinterest.com/website/verify/" class="external-link has-icon" rel="external noopener noreferrer">Pinterest Site Verification</a>, and <a target="_blank" href="https://webmaster.yandex.com/sites/" class="external-link has-icon" rel="external noopener noreferrer">Yandex.Webmaster</a>.</p>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">Google</span>
                            <input type="text" class="form-control" placeholder="" value="<?php echo $traffic_google; ?>" name="traffic_google">
                        </div>
                        <span class="form-text text-muted"><code><meta name="google-site-verification" content="1234" /></code></span>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">Bing</span>
                            <input type="text" class="form-control" placeholder="" value="<?php echo $traffic_bing; ?>" name="traffic_bing">
                        </div>
                        <span class="form-text text-muted"><code><meta name="msvalidate.01" content="1234" /></code></span>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">Pinterest</span>
                            <input type="text" class="form-control" placeholder="" value="<?php echo $traffic_pinterest; ?>" name="traffic_pinterest">
                        </div>
                        <span class="form-text text-muted"><code><meta name="p:domain_verify" content="1234" /></code></span>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">Yandex</span>
                            <input type="text" class="form-control" placeholder="" value="<?php echo $traffic_yandex; ?>" name="traffic_yandex">
                        </div>
                        <span class="form-text text-muted"><code><meta name="p:domain_verify" content="1234" /></code></span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>