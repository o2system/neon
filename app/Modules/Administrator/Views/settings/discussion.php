<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <form method="post">
                <div class="card-header d-flex align-items-center justify-content-between">
                    Default Article Setting
                    <div class="card-options">
                        <input type="submit" class="btn btn-primary btn-sm" name="articleSave" value="Save Settings"/>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_notification == 1 ? 'checked' : ''; ?> name="discuss_notification" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Allow Notification From Linked Articles
                        </label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_comments == 1 ? 'checked' : ''; ?> name="discuss_comments" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Allow Comments
                        </label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_allow_comments == 1 ? 'checked' : ''; ?> name="discuss_allow_comments" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Allow People Comment New Articles
                        </label>
                    </div>
                    <span class="form-text text-muted">These settings may be overridden for individual articles.</span>
                </div>
            </form>
        </div>

        <div class="card">
            <form method="post">
                <div class="card-header d-flex align-items-center justify-content-between">
                    Comments
                    <div class="card-options">
                        <input type="submit" class="btn btn-primary btn-sm" name="save" value="Save Settings"/>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_author_email == 1 ? 'checked' : ''; ?> name="discuss_author_email" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Comment Author Must Fill Email
                        </label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_must_login == 1 ? 'checked' : ''; ?> name="discuss_must_login" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Users must be registered and logged in to comment
                        </label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_close_comment == 1 ? 'checked' : ''; ?> name="discuss_close_comment" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Automatically close comments on articles older than <input style="width: 10%" value="<?php echo $discuss_close_comment_days; ?>" type="number" name="discuss_close_comment_days" class="form-control d-inline-block"/> days
                        </label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input <?php echo intval($enable_thread_comment) === 1 ? 'checked' : ''; ?> type="checkbox" value="1" name="enable_thread_comment" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Enable threaded (nested) comments up to <input style="width: 10%" type="number" name="thread_comment" value="<?php echo $thread_comment; ?>" class="form-control d-inline-block"/> levels deep
                        </label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_break_comments == 1 ? 'checked' : ''; ?> name="discuss_break_comments" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Break comments into pages with <input style="width: 10%" type="number" value="<?php echo $discuss_breaks; ?>" name="discuss_breaks" class="form-control d-inline-block"/> top level comments per page and the
                            <select class="form-control custom-select d-inline-block" style="width: 20%">
                                <option>First</option>
                                <option>Last</option>
                            </select>
                            page displayed by default
                        </label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_comment_top == 1 ? 'checked' : ''; ?> name="discuss_comment_top" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Comments should be displayed with the older comments at the top of each page
                        </label>
                    </div>
                </div>
                <hr class="m-0">
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label>Email Me Whenever</label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_notify_comment == 1 ? 'checked' : ''; ?> name="discuss_notify_comment" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Anyone posts a comment
                        </label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo intval($discuss_comment_held_mod) === 1 ? 'checked' : ''; ?> name="discuss_comment_held_mod" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            A comment is held for moderation
                        </label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_notify_like == 1 ? 'checked' : ''; ?> name="discuss_notify_like" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Someone likes one of my posts
                        </label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_notify_reblog == 1 ? 'checked' : ''; ?> name="discuss_notify_reblog" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Someone reblogs one of my posts
                        </label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_notify_follow == 1 ? 'checked' : ''; ?> name="discuss_notify_follow" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Someone follows my blog
                        </label>
                    </div>
                </div>
                <hr class="m-0">
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label>Before a comment appears</label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_moderation == 1 ? 'checked' : ''; ?> name="discuss_moderation" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Comment must be manually approved
                        </label>
                    </div>
                    <div class="form-group d-flex">
                        <div class="mt-0">
                            <input type="checkbox" value="1" <?php echo $discuss_approved == 1 ? 'checked' : ''; ?> name="discuss_approved" data-size="small" class="js-switch" data-color="#009efb"/>
                        </div>
                        <label class="mb-0 ml-3">
                            Comment author must have a previously approved comment
                        </label>
                    </div>
                </div>
                <hr class="m-0">
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label>Comment Moderation</label>
                    </div>
                    <p>Hold a comment in the queue if it contains <input style="width: 10%" type="number" name="comment_links_moderation" value="<?php echo $comment_links_moderation; ?>" value="10" class="form-control d-inline-block"/> or more links. (A common characteristic of comment spam is a large number of hyperlinks.)</p>
                    <p>When a comment contains any of these words in its content, name, URL, e-mail, or IP, it will be held in the moderation queue. One word or IP per line. It will match inside words.</p>
                    <div class="form-group">
                        <textarea class="form-control" name="discuss_words"/>
                            <?php echo $discuss_words; ?>
                        </textarea>
                    </div>
                </div>
                <hr class="m-0">
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label>Comment Blacklists</label>
                    </div>
                    <p>When a comment contains any of these words in its content, name, URL, e-mail, or IP, it will be held in the moderation queue. One word or IP per line. It will match inside words.</p>
                    <div class="form-group">
                        <textarea class="form-control" name="comment_blacklist"/><?php echo $comment_blacklist; ?></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>