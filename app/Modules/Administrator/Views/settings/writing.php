<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="list-group mb-3">
            <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">Categories</h6>
                </div>
                <p class="mb-0 text-muted"> <span class="fa fa-tag"></span> 1 Categories, default categories: Uncategorized</p>
            </a>
            <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">Tags</h6>
                </div>
                <p class="mb-0 text-muted"> <span class="fa fa-tag"></span> 0 Tags</p>
            </a>
        </div>

        <div class="card">
            <form method="post">
                <div class="card-header d-flex align-items-center justify-content-between">
                    Composing
                    <div class="card-options">
                        <input type="submit" class="btn btn-primary btn-sm" name="composingSave" value="Save Settings"/>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" name="write_privacy" data-size="small" class="js-switch" data-color="#009efb" <?php echo intval($write_privacy) == 1 ? 'checked' : ''; ?> value="1"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Show Publish Confirmation<br>
                            <span class="form-text text-muted">This adds a confirmation step with helpful settings and tips for double-checking your content before publishing.</span>
                        </label>
                    </div>
                </div>
                <hr class="m-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Default Post Format</label>
                            <select value="<?php echo $write_language; ?>" name="write_language" class="form-control custom-select">
                                <option value="STANDARD">Standard</option>
                            </select>
                        </div>
                    </div>
                </div>
                <hr class="m-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Default Time Format</label>
                            <select value="<?php echo $write_timestamp; ?>" name="write_timestamp" class="form-control custom-select">
                                <option value="yyyy-mm-dd">2017-01-11</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <form method="post">
                <div class="card-header d-flex align-items-center justify-content-between">
                    Content Types
                    <div class="card-options">
                        <input type="submit" class="btn btn-primary btn-sm" name="save" value="Save Settings"/>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="mb-0"><strong>Blog posts</strong></label>
                        <p class="mb-0 pl-5">Display <input style="width: 10%" type="number" value="<?php echo $write_pages; ?>" name="write_pages" class="form-control d-inline-block"/> blog posts per page</p>
                        <span class="form-text text-muted pl-5">On blog pages, the number of posts to show per page.</span>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" name="write_testimonial" data-size="small" class="js-switch" data-color="#009efb" <?php echo intval($write_testimonial) === 1 ? 'checked': ''; ?> value="1"/>
                        </div>
                        <label class="mb-0 ml-3">
                            <strong>Testimonials</strong>
                            <p class="mb-0 mt-2">Display <input style="width: 10%" type="number" value="<?php echo $write_testimonial_pages; ?>" name="write_testimonial_pages" class="form-control d-inline-block"/> testimonials per page</p>
                            <span class="form-text text-muted">Add, organize, and display testimonials. If your theme doesn’t support testimonials yet, you can display them using the shortcode [testimonials].</span>
                        </label>
                    </div>

                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" name="write_portfolio" data-size="small" class="js-switch" data-color="#009efb" <?php echo intval($write_portfolio) === 1 ? 'checked' : ''; ?> value="1"/>
                        </div>
                        <label class="mb-0 ml-3">
                            <strong>Portfolios</strong>
                            <p class="mb-0 mt-2">Display <input style="width: 10%" type="number" value="<?php echo $write_portfolio_pages; ?>" name="write_portfolio_pages" class="form-control d-inline-block"/> portfolio projects per page</p>
                            <span class="form-text text-muted">Add, organize, and display portfolio projects. If your theme doesn’t support portfolio projects yet, you can display them using the shortcode [portfolio].</span>
                        </label>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>