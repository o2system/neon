<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <form method="post" enctype="multipart/form-data">
                <div class="card-header d-flex align-items-center justify-content-between">
                    Site Setting
                    <div class="card-options">
                        <input type="submit" class="btn btn-primary btn-sm" name="saveSite" value="Save Settings"/>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Logo</label>
                                <input type="file" class="dropify" data-height="150" data-default-file="<?php echo $site_logo === '' ? assets_url('img/logo/logo-200px.png') : assets_url('img/'.$site_logo); ?>" data-allowed-file-extensions="jpg png" value="" name="site_logo"/>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Site Title</label>
                                    <input type="text" class="form-control" name="site_title" value="<?php echo $site_title; ?>"/>
                                </div>
                                <div class="form-group">
                                    <label>Site Tagline</label>
                                    <input type="text" class="form-control" name="site_tagline" value="<?php echo $site_tagline; ?>"/>
                                    <span class="form-text text-muted">In a few words, explain what this site is about.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="m-0">
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label>Site Address</label>
                    </div>
                    <div class="form-group mb-0 row">
                        <div class="col-md-9">
                            <input type="text" name="site_address" value="<?php echo $site_address; ?>" class="form-control" />
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-default btn-block">Change</button>
                        </div>
                    </div>
                </div>
                <hr class="m-0">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-12">Language</label>
                        <div class="col-md-6">
                            <select name="site_language" class="custom-select form-control">
                                <option <?php echo $site_language === 'ENGLISH' ? 'selected' : ''; ?> value="ENGLISH">English</option>
                                <option <?php echo $site_language === 'INDONESIA' ? 'selected' : ''; ?> value="INDONESIA">Indonesia</option>
                            </select>
                            <span class="form-text text-muted">Language this blog is primarily written in.</span>
                        </div>
                    </div>
                    <div class="form-group mb-0 row">
                        <label class="col-md-12">Timezone</label>
                        <div class="col-md-5">
                            <select name="site_timezone" value="<?php echo $site_timezone; ?>" class="form-control custom-select">
                                <option <?php echo $site_timezone === 'JAKARTA' ? 'selected' : ''; ?> value="JAKARTA">Jakarta</option>
                                <option <?php echo $site_timezone === 'BANDUNG' ? 'selected' : ''; ?> value="BANDUNG">Bandung</option>
                            </select>
                            <span class="form-text text-muted">Choose a city in your timezone.</span>
                        </div>
                    </div>
                </div>
                <hr class="m-0">
                <div class="card-body">
                    <div class="form-group mb-0 d-flex justify-content-between">
                        <label class="mb-0">Site Offline</label>
                        <input type="checkbox" <?php echo $site_offline === 'on' ? 'checked' : ''; ?> name="site_offline" data-size="small" class="js-switch" data-color="#009efb" />
                    </div>
                </div>
            </form>
        </div>
        <div class="card">
            <form method="post">
                <div class="card-header d-flex align-items-center justify-content-between">
                    Privacy
                    <div class="card-options">
                        <input type="submit" class="btn btn-primary btn-sm" name="savePrivacy" value="Save Settings"/>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="radio radio-primary">
                            <input name="site_privacy" id="privacy1" value="1" type="radio" <?php echo intval($site_privacy) == 1 ? 'checked' : ''; ?>>
                            <label for="privacy1">
                                Public<br>
                                <span class="form-text text-muted">Your site is visible to everyone, and it may be indexed by search engines.</span>
                            </label>
                        </div>
                        <div class="radio radio-primary">
                            <input name="site_privacy" id="privacy2" value="2" type="radio" <?php echo intval($site_privacy) == 2 ? 'checked' : ''; ?>>
                            <label for="privacy2">
                                Hidden<br>
                                <span class="form-text text-muted">Your site is visible to everyone, but we ask search engines to not index your site.</span>
                            </label>
                        </div>
                        <div class="radio radio-primary">
                            <input name="site_privacy" id="privacy3" value="3" type="radio" <?php echo intval($site_privacy) == 3 ? 'checked' : ''; ?>>
                            <label for="privacy3">
                                Private<br>
                                <span class="form-text text-muted">Your site is only visible to you and users you approve.</span>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card">
            <form method="post">
                <div class="card-header d-flex align-items-center justify-content-between">
                    Site Metadata
                    <div class="card-options">
                        <input type="submit" class="btn btn-primary btn-sm" name="saveMeta" value="Save Settings"/>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Metarobot</label>
                        <select class="form-control custom-select" name="meta_robot">
                            <option <?php echo $meta_robot === 'sightseeing' ? 'selected' : ''; ?>  value="none">None </option>
                            <option <?php echo $meta_robot === 'noodp' ? 'selected' : ''; ?> value="noodp">No ODP </option>
                            <option <?php echo $meta_robot === 'noydir' ? 'selected' : ''; ?> value="noydir">No YDIR </option>
                            <option <?php echo $meta_robot === 'noarchive' ? 'selected' : ''; ?> value="noarchive">No Archive </option>
                            <option <?php echo $meta_robot === 'nosnippet' ? 'selected' : ''; ?> value="nosnippet">No Snippet </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Metatitle</label>
                        <input class="form-control" value="<?php echo $meta_title; ?>" name="meta_title" type="text">
                    </div>
                    <div class="form-group">
                        <label>Metadescription</label>
                        <textarea class="form-control" cols="5" name="meta_description"><?php echo $meta_description; ?></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label class="d-block">Metakeywords</label>
                        <input name="meta_keywords" class="tagsinput form-control" data-role="tagsinput" value="<?php echo $meta_keywords; ?>" type="text">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
