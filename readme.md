## WordPress Plugin: "Was this article helpful?"

### Description:

This WordPress plugin allows your site visitors to vote on your articles. It is easy to use, efficient and secure, and integrates seamlessly with WordPress sites.

### Features:

- Simple voting system with two buttons - "Yes" and "No".
- Each button adds a "positive" or "negative" vote to the voting results.
- Voting results are displayed as a percentage.
- Voting takes place using an Ajax request.
- After voting, the visitor sees the voting results as a percentage, and the buttons become inactive, but also show their vote (even after refreshing the page).
- Visitors cannot vote for the same article twice. This is done using the visitor's cookie.
- The voting function is automatically displayed at the end of any blog post.
- Voting elements are responsive and work well on different devices and screen sizes.

### Admin Functionality:

When editing an article in the admin area, you can see the voting results in the metadata widget.

### How to install:

- Upload the plugin folder (simple-voting) to the /wp-content/plugins/ directory on your WordPress server.
- Activate Plugin on Plugins page in admin area
- New block with voting will appear after posts' content
- New meta-box will appear in side-area on Edit-Post admin page

### Testing

- For developers there has been added testing cases.
- To set up the testing environment, you need to download the necessary vendor libraries using the composer. To do this, open the folder with the plugin in the terminal and execute the command `composer install`. If it needs, read how to install composer.
- To run the tests, you need to open the folder with the plugin in the terminal and run the command: `vendor/bin/phpunit`
- Tests do not cover all functions, it was added for future development as a skeleton.