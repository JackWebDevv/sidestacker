-- Create tools table
CREATE TABLE IF NOT EXISTS tools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    type ENUM('free', 'premium') NOT NULL,
    icon_class VARCHAR(100) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    sort_order INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Insert tools data
INSERT INTO tools (name, description, category, type, icon_class, slug, sort_order) VALUES
-- Business & Finance Tools
('CV Builder', 'Create professional resumes and CVs with our easy-to-use builder', 'Career', 'premium', 'fa-file-alt', 'cv-builder', 10),
('Business Plan Builder', 'Generate comprehensive business plans with our intuitive builder', 'Business', 'premium', 'fa-briefcase', 'business-plan-builder', 20),
('TikTok Content Idea Generator', 'Generate viral TikTok content ideas instantly', 'Social Media', 'free', 'fa-video', 'tiktok-content-generator', 30),
('Business Name Generator', 'Generate unique and memorable business names', 'Business', 'free', 'fa-building', 'business-name-generator', 40),
('Side Hustle Idea Generator', 'Discover profitable side hustle opportunities', 'Business', 'free', 'fa-lightbulb', 'side-hustle-generator', 50),
('Passive Income Calculator', 'Calculate potential passive income streams', 'Finance', 'free', 'fa-calculator', 'passive-income-calculator', 60),
('ROI Calculator', 'Calculate return on investment for your business decisions', 'Finance', 'free', 'fa-chart-line', 'roi-calculator', 70),
('Product Research Tool', 'Research and analyze potential products to sell', 'E-commerce', 'premium', 'fa-search', 'product-research-tool', 80),
('Lead Web Scraper', 'Extract leads from websites automatically', 'Marketing', 'premium', 'fa-spider', 'lead-web-scraper', 90),
('Profit Margin Calculator', 'Calculate profit margins for your products', 'Finance', 'free', 'fa-percentage', 'profit-margin-calculator', 100),
('Invoice Builder', 'Create professional invoices with custom branding', 'Business', 'premium', 'fa-file-invoice', 'invoice-builder', 110),
('Supplier Finder', 'Find reliable suppliers for your business', 'E-commerce', 'premium', 'fa-truck', 'supplier-finder', 120),
('Store Name Generator', 'Generate catchy names for your online store', 'E-commerce', 'free', 'fa-store', 'store-name-generator', 130),
('Pricing Strategy Tools', 'Develop optimal pricing strategies', 'Business', 'premium', 'fa-tags', 'pricing-strategy-tools', 140),
('Shipping Cost Calculator', 'Calculate shipping costs for your products', 'E-commerce', 'free', 'fa-shipping-fast', 'shipping-calculator', 150),

-- Content Creation Tools
('eBook Title Generator', 'Generate compelling eBook titles', 'Content', 'free', 'fa-book', 'ebook-title-generator', 160),
('Digital Product Idea Generator', 'Generate ideas for digital products', 'Business', 'free', 'fa-digital-tachograph', 'digital-product-generator', 170),
('Sales Page Analyser', 'Analyze and optimize your sales pages', 'Marketing', 'premium', 'fa-chart-bar', 'sales-page-analyser', 180),
('Freelance Rate Calculator', 'Calculate optimal freelance rates', 'Finance', 'free', 'fa-money-bill', 'freelance-rate-calculator', 190),
('Portfolio Builder', 'Create stunning portfolio websites', 'Career', 'premium', 'fa-palette', 'portfolio-builder', 200),
('Website Builder', 'Build professional websites easily', 'Development', 'premium', 'fa-globe', 'website-builder', 210),
('Client Proposal Generator', 'Generate professional client proposals', 'Business', 'premium', 'fa-file-contract', 'proposal-generator', 220),
('Gig Title Generator', 'Create attention-grabbing gig titles', 'Freelance', 'free', 'fa-heading', 'gig-title-generator', 230),
('Gig Description Optimiser', 'Optimize your gig descriptions', 'Freelance', 'premium', 'fa-edit', 'gig-description-optimiser', 240),
('Fiverr Pricing Calculator', 'Calculate optimal prices for Fiverr gigs', 'Freelance', 'free', 'fa-dollar-sign', 'fiverr-pricing-calculator', 250),

-- SEO & Marketing Tools
('Keyword Difficulty Checker', 'Check keyword competition levels', 'SEO', 'premium', 'fa-chart-line', 'keyword-difficulty-checker', 260),
('Meta Description Generator', 'Generate SEO-friendly meta descriptions', 'SEO', 'free', 'fa-align-left', 'meta-description-generator', 270),
('Backlink Analyser', 'Analyze your backlink profile', 'SEO', 'premium', 'fa-link', 'backlink-analyser', 280),
('Affiliate Niche Finder', 'Find profitable affiliate niches', 'Marketing', 'premium', 'fa-search-dollar', 'affiliate-niche-finder', 290),
('Commission Calculator', 'Calculate affiliate commissions', 'Finance', 'free', 'fa-percentage', 'commission-calculator', 300),
('Affiliate Link Cloaker', 'Create branded affiliate links', 'Marketing', 'premium', 'fa-link', 'affiliate-link-cloaker', 310),
('Hashtag Generator', 'Generate relevant hashtags for social media', 'Social Media', 'free', 'fa-hashtag', 'hashtag-generator', 320),
('Post Scheduler', 'Schedule social media posts', 'Social Media', 'premium', 'fa-calendar', 'post-scheduler', 330),
('Engagement Rate Calculator', 'Calculate social media engagement rates', 'Social Media', 'free', 'fa-chart-pie', 'engagement-calculator', 340),

-- Content & Course Creation
('Blog Title Generator', 'Generate engaging blog titles', 'Content', 'free', 'fa-heading', 'blog-title-generator', 350),
('Content Calendar Tool', 'Plan and organize your content', 'Content', 'premium', 'fa-calendar-alt', 'content-calendar', 360),
('Plagiarism Checker', 'Check content for plagiarism', 'Content', 'premium', 'fa-copy', 'plagiarism-checker', 370),
('Email Subject Line Generator', 'Generate high-converting email subjects', 'Marketing', 'free', 'fa-envelope', 'email-subject-generator', 380),
('Email Template Builder', 'Create professional email templates', 'Marketing', 'premium', 'fa-envelope-open-text', 'email-template-builder', 390),
('Open Rate Optimiser', 'Optimize email open rates', 'Marketing', 'premium', 'fa-chart-line', 'open-rate-optimiser', 400),
('Course Outline Generator', 'Generate course outlines easily', 'Education', 'free', 'fa-list', 'course-outline-generator', 410),
('Pricing Strategy Tool for Courses', 'Optimize your course pricing', 'Education', 'premium', 'fa-dollar-sign', 'course-pricing-tool', 420),
('Course Landing Page Analyser', 'Analyze course landing pages', 'Education', 'premium', 'fa-analytics', 'landing-page-analyser', 430),

-- Design & Development Tools
('Design Idea Generator', 'Generate creative design ideas', 'Design', 'free', 'fa-paint-brush', 'design-idea-generator', 440),
('Product Mock-up Generator', 'Create product mock-ups instantly', 'Design', 'premium', 'fa-box', 'mockup-generator', 450),
('Profit Calculator for Print on Demand', 'Calculate POD profits', 'E-commerce', 'free', 'fa-tshirt', 'pod-calculator', 460),
('Crypto Profit Calculator', 'Calculate cryptocurrency profits', 'Finance', 'free', 'fa-bitcoin', 'crypto-calculator', 470),
('NFT Idea Generator', 'Generate unique NFT ideas', 'Crypto', 'free', 'fa-images', 'nft-generator', 480),
('Wallet Address Validator', 'Validate cryptocurrency wallet addresses', 'Crypto', 'free', 'fa-wallet', 'wallet-validator', 490),
('Password Strength Checker', 'Check password security strength', 'Security', 'free', 'fa-lock', 'password-checker', 500),
('Data Breach Checker', 'Check if your data has been compromised', 'Security', 'free', 'fa-shield-alt', 'breach-checker', 510),
('Two-Factor Authentication Guide', 'Setup 2FA security', 'Security', 'free', 'fa-key', '2fa-guide', 520),
('Code Snippet Generator', 'Generate common code snippets', 'Development', 'free', 'fa-code', 'code-generator', 530),
('Website Uptime Monitor', 'Monitor website availability', 'Development', 'premium', 'fa-server', 'uptime-monitor', 540),
('API Documentation Generator', 'Generate API documentation', 'Development', 'premium', 'fa-file-code', 'api-docs-generator', 550),

-- Productivity & Career Tools
('Remote Job Finder', 'Find remote work opportunities', 'Career', 'free', 'fa-laptop-house', 'remote-job-finder', 560),
('Time Zone Converter', 'Convert between time zones', 'Productivity', 'free', 'fa-clock', 'timezone-converter', 570),
('Productivity Tracker', 'Track your productivity', 'Productivity', 'premium', 'fa-tasks', 'productivity-tracker', 580),
('Product Research Tool for Amazon', 'Research Amazon products', 'E-commerce', 'premium', 'fa-amazon', 'amazon-research-tool', 590),
('FBA Fee Calculator', 'Calculate Amazon FBA fees', 'E-commerce', 'free', 'fa-calculator', 'fba-calculator', 600),
('Keyword Research Tool for Amazon', 'Find profitable Amazon keywords', 'E-commerce', 'premium', 'fa-search', 'amazon-keyword-tool', 610),

-- Marketing & Branding Tools
('Personal Branding Checklist', 'Build your personal brand', 'Marketing', 'free', 'fa-id-badge', 'branding-checklist', 620),
('Social Media Bio Generator', 'Create engaging social media bios', 'Social Media', 'free', 'fa-user-edit', 'bio-generator', 630),
('Website About Page Generator', 'Generate about page content', 'Content', 'free', 'fa-info-circle', 'about-generator', 640),
('Video Title Generator', 'Create engaging video titles', 'Content', 'free', 'fa-video', 'video-title-generator', 650),
('Thumbnail Maker', 'Create eye-catching thumbnails', 'Design', 'premium', 'fa-image', 'thumbnail-maker', 660),
('Ad Revenue Calculator', 'Calculate advertising revenue', 'Finance', 'free', 'fa-ad', 'ad-revenue-calculator', 670),
('Dividend Calculator', 'Calculate dividend returns', 'Finance', 'free', 'fa-chart-pie', 'dividend-calculator', 680),
('Real Estate ROI Calculator', 'Calculate real estate returns', 'Finance', 'free', 'fa-home', 'real-estate-calculator', 690),
('Royalty Income Estimator', 'Estimate royalty earnings', 'Finance', 'free', 'fa-crown', 'royalty-calculator', 700),

-- E-commerce & Product Tools
('Product Description Generator', 'Generate product descriptions', 'E-commerce', 'premium', 'fa-box-open', 'description-generator', 710),
('Tag Generator', 'Generate product tags', 'E-commerce', 'free', 'fa-tags', 'tag-generator', 720),
('Pricing Calculator for Handmade Goods', 'Price handmade items', 'E-commerce', 'free', 'fa-hand-holding-usd', 'handmade-calculator', 730),
('App Idea Generator', 'Generate app ideas', 'Development', 'free', 'fa-mobile-alt', 'app-idea-generator', 740),
('App Revenue Calculator', 'Calculate potential app revenue', 'Finance', 'free', 'fa-mobile', 'app-revenue-calculator', 750),
('Wireframe Tool', 'Create website wireframes', 'Design', 'premium', 'fa-pencil-ruler', 'wireframe-tool', 760),
('Colour Palette Generator', 'Generate color schemes', 'Design', 'free', 'fa-palette', 'color-palette', 770),
('Font Pairing Tool', 'Find perfect font combinations', 'Design', 'free', 'fa-font', 'font-pairing', 780),
('Website Speed Analyser', 'Analyze website performance', 'Development', 'free', 'fa-tachometer-alt', 'speed-analyser', 790),

-- Business Analytics Tools
('Marketplace Fee Calculator', 'Calculate marketplace fees', 'E-commerce', 'free', 'fa-percentage', 'marketplace-calculator', 800),
('Competitor Price Tracker', 'Track competitor prices', 'E-commerce', 'premium', 'fa-binoculars', 'price-tracker', 810),
('Listing Optimiser', 'Optimize product listings', 'E-commerce', 'premium', 'fa-list-alt', 'listing-optimiser', 820),
('Cost of Living Calculator', 'Calculate living costs by location', 'Finance', 'free', 'fa-home', 'cost-of-living', 830),
('Visa Requirement Checker', 'Check visa requirements', 'Travel', 'free', 'fa-passport', 'visa-checker', 840),
('Remote Work Hub Finder', 'Find remote work spaces', 'Career', 'free', 'fa-building', 'hub-finder', 850),
('Profile Score Analyser', 'Analyze online profiles', 'Career', 'premium', 'fa-user-check', 'profile-analyser', 860),
('Bid Proposal Generator', 'Generate bid proposals', 'Business', 'premium', 'fa-file-signature', 'bid-generator', 870),
('Freelance Contract Generator', 'Generate freelance contracts', 'Legal', 'premium', 'fa-file-contract', 'contract-generator', 880),

-- AI & Automation Tools
('AI Content Rewriter', 'Rewrite content with AI', 'Content', 'premium', 'fa-robot', 'ai-rewriter', 890),
('Chatbot Builder', 'Create custom chatbots', 'Development', 'premium', 'fa-comments', 'chatbot-builder', 900),
('Automation Workflow Designer', 'Design automation workflows', 'Productivity', 'premium', 'fa-random', 'workflow-designer', 910),
('Stock Screener', 'Screen stocks by criteria', 'Finance', 'premium', 'fa-chart-line', 'stock-screener', 920),
('Risk Tolerance Analyser', 'Analyze investment risk tolerance', 'Finance', 'free', 'fa-chart-bar', 'risk-analyser', 930),
('Portfolio Diversification Tool', 'Optimize investment portfolio', 'Finance', 'premium', 'fa-chart-pie', 'portfolio-tool', 940),
('Churn Rate Calculator', 'Calculate customer churn rate', 'Business', 'free', 'fa-user-minus', 'churn-calculator', 950),
('Loyalty Programme Builder', 'Create loyalty programs', 'Marketing', 'premium', 'fa-award', 'loyalty-builder', 960),
('Customer Feedback Analyser', 'Analyze customer feedback', 'Business', 'premium', 'fa-comments', 'feedback-analyser', 970),
('Crowdfunding Goal Calculator', 'Calculate crowdfunding goals', 'Finance', 'free', 'fa-hand-holding-usd', 'crowdfunding-calculator', 980),
('Reward Tier Generator', 'Generate reward tiers', 'Marketing', 'free', 'fa-trophy', 'reward-generator', 990),
('Campaign Tracker', 'Track marketing campaigns', 'Marketing', 'premium', 'fa-bullseye', 'campaign-tracker', 1000),

-- Security & Compliance Tools
('VPN Comparison Tool', 'Compare VPN services', 'Security', 'free', 'fa-shield-alt', 'vpn-comparison', 1010),
('Password Generator', 'Generate secure passwords', 'Security', 'free', 'fa-key', 'password-generator', 1020),
('Privacy Policy Generator', 'Generate privacy policies', 'Legal', 'free', 'fa-file-alt', 'privacy-generator', 1030),
('Growth Strategy Planner', 'Plan business growth strategy', 'Business', 'premium', 'fa-chart-line', 'growth-planner', 1040),
('Team Role Assigner', 'Assign team roles efficiently', 'Management', 'free', 'fa-users', 'role-assigner', 1050),
('KPI Tracker', 'Track key performance indicators', 'Business', 'premium', 'fa-chart-bar', 'kpi-tracker', 1060);
