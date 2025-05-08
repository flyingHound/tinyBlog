-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 08. Mai 2025 um 00:38
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `tinyblog`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `associated_blog_tags_and_blog_posts`
--

CREATE TABLE `associated_blog_tags_and_blog_posts` (
  `id` int(11) NOT NULL,
  `blog_tags_id` int(11) NOT NULL DEFAULT 0,
  `blog_posts_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `associated_blog_tags_and_blog_posts`
--

INSERT INTO `associated_blog_tags_and_blog_posts` (`id`, `blog_tags_id`, `blog_posts_id`) VALUES
(3, 6, 2),
(4, 5, 2),
(5, 4, 2),
(6, 1, 4),
(7, 2, 4),
(8, 4, 4),
(10, 6, 9),
(11, 5, 9),
(12, 6, 13),
(22, 2, 1),
(30, 1, 1),
(36, 3, 1),
(37, 6, 1),
(38, 5, 1),
(39, 6, 14),
(40, 5, 14),
(41, 4, 13),
(42, 5, 13),
(43, 3, 13),
(44, 3, 2),
(45, 1, 2),
(46, 1, 18),
(47, 4, 9),
(48, 4, 12);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `url_string` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `title`, `url_string`) VALUES
(1, 'Food', 'food'),
(2, 'Sports', 'sports'),
(3, 'Travel', 'travel'),
(4, 'Magazine', 'magazine'),
(5, 'Health', 'health');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blog_pictures`
--

CREATE TABLE `blog_pictures` (
  `id` int(11) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `target_module` varchar(255) DEFAULT NULL,
  `target_module_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `blog_pictures`
--

INSERT INTO `blog_pictures` (`id`, `picture`, `priority`, `target_module`, `target_module_id`) VALUES
(59, '20220428-135337_sf4K.jpg', 1, 'blog_posts', 1),
(60, '20210405-090544_7ay7.jpg', 2, 'blog_posts', 1),
(72, '20240914-141353_pDM4.jpg', 5, 'blog_posts', 1),
(73, '20240914-141354_z3sb.jpg', 6, 'blog_posts', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `url_string` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `youtube` varchar(55) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_published` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `published` int(1) DEFAULT NULL,
  `picture` varchar(255) DEFAULT '',
  `blog_sources_id` int(11) DEFAULT 0,
  `blog_categories_id` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `url_string`, `title`, `subtitle`, `text`, `youtube`, `date_created`, `date_updated`, `date_published`, `created_by`, `updated_by`, `published`, `picture`, `blog_sources_id`, `blog_categories_id`) VALUES
(1, 'the-making-of-tinyblog', 'The Making of &lt;span class=&quot;font-chau&quot; style=&quot;color: #112233;&quot;&gt;&lt;span style=&quot;color: #5bc0eb;&quot;&gt;tiny&lt;/span&gt;Blog&lt;/span&gt;', '', '&lt;h2&gt;Your Professional Blogging Solution&lt;/h2&gt;\r\n&lt;p&gt;Elevate your online presence with &lt;span class=&quot;font-chau&quot; style=&quot;color: #112233;&quot;&gt;&lt;span style=&quot;color: #5bc0eb;&quot;&gt;tiny&lt;/span&gt;Blog&lt;/span&gt;, a robust and intuitive platform designed to empower you to create and manage a professional blog effortlessly. Whether you&#039;re a seasoned writer or just starting out, &lt;span class=&quot;font-chau&quot; style=&quot;color: #112233;&quot;&gt;&lt;span style=&quot;color: #5bc0eb;&quot;&gt;tiny&lt;/span&gt;Blog&lt;/span&gt; equips you with all the tools you need to craft engaging, meaningful content and connect with your audience.&lt;/p&gt;\r\n&lt;h3&gt;Key Features&lt;/h3&gt;\r\n&lt;h4&gt;Seamless Content Creation&lt;/h4&gt;\r\n&lt;ul&gt;\r\n&lt;li&gt;&lt;strong&gt;Rich Text Editing&lt;/strong&gt;: Utilize a powerful WYSIWYG editor powered by&amp;nbsp;&lt;strong&gt;TinyMCE&lt;/strong&gt;, enabling you to create visually appealing posts with ease. Format headlines, text, and embed HTML tags effortlessly.&lt;/li&gt;\r\n&lt;li&gt;&lt;strong&gt;Flexible Media Integration&lt;/strong&gt;: Enhance your posts by embedding images within the text or showcasing them prominently above articles. Link **YouTube videos** or create dynamic&amp;nbsp;&lt;strong&gt;image galleries&lt;/strong&gt;&amp;nbsp;with intuitive sorting capabilities.&lt;/li&gt;\r\n&lt;li&gt;&lt;strong&gt;Content Organization&lt;/strong&gt;: Assign&amp;nbsp;&lt;strong&gt;categories&lt;/strong&gt;,&amp;nbsp;&lt;strong&gt;tags&lt;/strong&gt;, &lt;strong&gt;sources&lt;/strong&gt;, and multiple&amp;nbsp;&lt;strong&gt;authors&lt;/strong&gt;&amp;nbsp;to your posts, ensuring your content is well-structured and easily discoverable.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;h4&gt;Built on the Planet&#039;s fastest High-Performance Framework&lt;/h4&gt;\r\n&lt;ul&gt;\r\n&lt;li&gt;Powered by the&amp;nbsp;&lt;strong&gt;Trongate PHP Framework&lt;/strong&gt;, &lt;span class=&quot;font-chau&quot; style=&quot;color: #112233;&quot;&gt;&lt;span style=&quot;color: #5bc0eb;&quot;&gt;tiny&lt;/span&gt;Blog&lt;/span&gt; offers lightning-fast performance, allowing you to create pages, manage site navigation, and publish blog posts in record time.&lt;/li&gt;\r\n&lt;li&gt;Streamlined workflows ensure you spend more time writing and less time managing technical details.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;h4&gt;Intuitive Media Management&lt;/h4&gt;\r\n&lt;ul&gt;\r\n&lt;li&gt;&lt;strong&gt;Effortless Image Handling&lt;/strong&gt;: Upload, organize, store, and delete images and galleries with a user-friendly interface designed for simplicity and efficiency.&lt;/li&gt;\r\n&lt;li&gt;&lt;strong&gt;Gallery Creation&lt;/strong&gt;: Build and manage stunning picture galleries to complement your content, with drag-and-drop sorting for quick customization.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;h4&gt;Engaging Reader Interaction&lt;/h4&gt;\r\n&lt;ul&gt;\r\n&lt;li&gt;&lt;strong&gt;Enquiry System&lt;/strong&gt;: Allow visitors to connect with you directly through a built-in contact form.&lt;/li&gt;\r\n&lt;li&gt;&lt;strong&gt;Future Enhancements&lt;/strong&gt;: Planned features include reader&amp;nbsp;&lt;strong&gt;comments&lt;/strong&gt;, post &lt;strong&gt;ranking&lt;/strong&gt;, and customizable &lt;strong&gt;settings&lt;/strong&gt;&amp;nbsp;to further engage your audience.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;h4&gt;Comprehensive Administration&lt;/h4&gt;\r\n&lt;ul&gt;\r\n&lt;li&gt;Manage your blog with a sleek, &lt;strong&gt;one-page Bootstrap dashboard&lt;/strong&gt; that provides a clear overview of your content, media, and site analytics at a glance.&lt;/li&gt;\r\n&lt;li&gt;Easily navigate and update&amp;nbsp;&lt;strong&gt;posts&lt;/strong&gt;,&amp;nbsp;&lt;strong&gt;categories&lt;/strong&gt;,&amp;nbsp;&lt;strong&gt;tags&lt;/strong&gt;,&amp;nbsp;&lt;strong&gt;menus&lt;/strong&gt;, &lt;strong&gt;sources&lt;/strong&gt; and&amp;nbsp;&lt;strong&gt;authors&lt;/strong&gt;&amp;nbsp;from a centralized interface.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;h3&gt;Core Components&lt;/h3&gt;\r\n&lt;h4&gt;Frontpage&lt;/h4&gt;\r\n&lt;ul&gt;\r\n&lt;li&gt;Display&amp;nbsp;&lt;strong&gt;single posts&lt;/strong&gt;,&amp;nbsp;&lt;strong&gt;all posts&lt;/strong&gt;, or filter by&amp;nbsp;&lt;strong&gt;category&lt;/strong&gt; or&amp;nbsp;&lt;strong&gt;tag&lt;/strong&gt;.&lt;/li&gt;\r\n&lt;li&gt;Customizable&amp;nbsp;&lt;strong&gt;widgets&lt;/strong&gt;&amp;nbsp;for main content and sidebar, including:&lt;/li&gt;\r\n&lt;li style=&quot;list-style-type: none;&quot;&gt;\r\n&lt;ul&gt;\r\n&lt;li&gt;&lt;strong&gt;Category Navigation&lt;/strong&gt;: Seamless browsing by category.&lt;/li&gt;\r\n&lt;li&gt;&lt;strong&gt;Picture Gallery&lt;/strong&gt;: Showcase your images dynamically.&lt;/li&gt;\r\n&lt;li&gt;&lt;strong&gt;YouTube Video Integration&lt;/strong&gt;: Embed videos effortlessly.&lt;/li&gt;\r\n&lt;li&gt;&lt;strong&gt;Sidebar Features&lt;/strong&gt;: Category counters, tag clouds, random images, and recent posts for enhanced user engagement.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;h4&gt;Blog Posts&lt;/h4&gt;\r\n&lt;ul&gt;\r\n&lt;li&gt;Create and manage posts with rich formatting and media integration.&lt;/li&gt;\r\n&lt;li&gt;Assign metadata such as &lt;strong&gt;categories&lt;/strong&gt;, &lt;strong&gt;tags&lt;/strong&gt;, and &lt;strong&gt;sources&lt;/strong&gt; for better organization and SEO.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;h4&gt;Picture Gallery&lt;/h4&gt;\r\n&lt;p&gt;Manage images and galleries through a dedicated &lt;strong&gt;filezone&lt;/strong&gt;, ensuring efficient storage and retrieval.&lt;/p&gt;\r\n&lt;h4&gt;Menus &amp;amp; Enquiries&lt;/h4&gt;\r\n&lt;ul&gt;\r\n&lt;li&gt;Customize site navigation with dynamic &lt;strong&gt;menus&lt;/strong&gt;.&lt;/li&gt;\r\n&lt;li&gt;Handle reader inquiries through an integrated&amp;nbsp;&lt;strong&gt;enquiry system&lt;/strong&gt;.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;h3&gt;Why Choose &lt;span class=&quot;font-chau&quot; style=&quot;color: #112233;&quot;&gt;&lt;span style=&quot;color: #5bc0eb;&quot;&gt;tiny&lt;/span&gt;Blog&lt;/span&gt;?&lt;/h3&gt;\r\n&lt;p&gt;&lt;span class=&quot;font-chau&quot; style=&quot;color: #112233;&quot;&gt;&lt;span style=&quot;color: #5bc0eb;&quot;&gt;tiny&lt;/span&gt;Blog&lt;/span&gt; is designed with a focus on simplicity, performance, and flexibility, making it the ideal choice for bloggers who want to create professional, engaging content without the complexity. Whether you&#039;re sharing stories, showcasing a portfolio, or building a community, &lt;span class=&quot;font-chau&quot; style=&quot;color: #112233;&quot;&gt;&lt;span style=&quot;color: #5bc0eb;&quot;&gt;tiny&lt;/span&gt;Blog&lt;/span&gt; provides a seamless and powerful platform to bring your vision to life.&lt;/p&gt;\r\n&lt;p&gt;Get started today and transform your ideas into a captivating blog with &lt;span class=&quot;font-chau&quot; style=&quot;color: #112233;&quot;&gt;&lt;span style=&quot;color: #5bc0eb;&quot;&gt;tiny&lt;/span&gt;Blog&lt;/span&gt;.&lt;/p&gt;', '97icE7kQ8DA', '2025-03-18 00:37:01', '0000-00-00 00:00:00', '2025-03-17 23:09:00', 1, 1, 1, '20240907_114540.jpg', 1, 3),
(2, 'new-post-record', '&lt;span style=&quot;background-color:#99FF99; color: white;&quot;&gt;New&lt;/span&gt; Post Record', 'Additional Subheadline', '&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;', '', '2025-03-18 20:10:22', '0000-00-00 00:00:00', '2025-03-18 20:09:00', 1, 1, 1, '', 8, 1),
(7, 'consectetur-adipisicing-elit', 'Consectetur adipisicing elit', '', '&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;', '', '2025-04-03 00:57:34', '0000-00-00 00:00:00', '2025-04-03 00:56:00', 1, 1, 1, '', 1, 4),
(8, 'sit-amet-consectetur-adipisicing-elit', 'Sit amet, consectetur adipisicing elit', 'Adipisci officiis enim', '&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;', '', '2025-04-03 23:30:40', '2025-04-03 23:36:34', '2025-04-03 23:30:00', 1, 1, 1, '', 3, 5),
(9, 'vitae-architecto-sunt-obcaecati-doloribus-deserunt', 'Vitae architecto sunt obcaecati doloribus deserunt', '', '&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.&amp;nbsp;Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;\r\n&lt;p&gt;Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;', '', '2025-04-05 09:57:32', '2025-04-07 08:49:33', '2025-04-05 09:57:00', 1, 1, 1, '', 0, 1),
(10, 'sit-sint-perferendis-a-totam', 'Sit sint perferendis a totam.', '', '&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;', '', '2025-04-06 18:20:04', '2025-05-07 00:59:11', '2025-04-06 18:18:00', 1, 1, 1, '', 0, 1),
(11, 'architecto-sunt-obcaecati-doloribus', 'Architecto sunt obcaecati doloribus', '', '&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;', '', '2025-04-07 01:05:07', '0000-00-00 00:00:00', '2025-04-07 01:04:00', 1, 1, 1, '', 0, 4),
(12, 'unde-molestiae-maxime', 'Unde, molestiae maxime.', '', '&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;', '', '2025-04-15 20:41:47', '0000-00-00 00:00:00', '2025-04-15 20:41:00', 1, 1, 1, '', 0, 4),
(13, 'set-aliquam-facilis-lorem', 'Set aliquam facilis lorem', 'Totam repellendus vitae', '&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.&lt;/p&gt;\r\n&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;', '', '2025-04-17 17:58:46', '2025-04-18 08:48:08', '2025-04-17 17:52:00', 1, 1, 1, '', 0, 2),
(15, 'consectetur-aliquam-facilis', 'Consectetur aliquam facilis', '', '&lt;p&gt;Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit.&lt;/p&gt;\r\n&lt;p&gt;Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;', '', '2025-04-27 09:32:08', '2025-04-27 10:32:34', '2025-04-27 09:29:00', 1, 1, 1, '', 1, 5),
(18, 'this-is-the-story-of-a-dog-who-kept-running-away-not-to-escape-but-to-be-found', 'This is the story of a dog who kept running away, not to escape, but to be found.', '', '&lt;p&gt;Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.&lt;/p&gt;\r\n&lt;p&gt;Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.&lt;/p&gt;', '', '2025-05-02 01:17:39', '2025-05-03 09:35:57', '2025-05-02 01:16:00', 2, 1, 1, '20211114_191320.jpg', 0, 1),
(20, 'an-empty-post', 'An empty post', '', NULL, '', '2025-05-07 10:39:35', '2025-05-07 13:27:14', '2025-05-07 10:39:00', 1, 1, 0, '', 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blog_sources`
--

CREATE TABLE `blog_sources` (
  `id` int(11) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `blog_sources`
--

INSERT INTO `blog_sources` (`id`, `author`, `website`, `link`) VALUES
(1, 'Owner', 'tinyBlog', 'http://localhost/tinyblog'),
(2, 'Chat GPT', 'Open AI', 'https://chatgpt.com'),
(3, 'Grok', 'grok.com', 'https://grok.com/');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blog_tags`
--

CREATE TABLE `blog_tags` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `url_string` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `blog_tags`
--

INSERT INTO `blog_tags` (`id`, `name`, `url_string`) VALUES
(1, 'WebDev', 'webdev'),
(2, 'Tutorial', 'tutorial'),
(3, 'Tech', 'tech'),
(4, 'Life', 'life'),
(5, 'Design', 'design'),
(6, 'Coding', 'coding');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `enquiries`
--

CREATE TABLE `enquiries` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `date_created` int(11) DEFAULT NULL,
  `opened` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `enquiries`
--

INSERT INTO `enquiries` (`id`, `name`, `email_address`, `message`, `date_created`, `opened`) VALUES
(1, 'Cash Catel', 'cash@gmail.com', 'Sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.', 1745706045, 1),
(2, 'Doug Burgun', 'doug@gmail.com', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 1745707167, 1),
(3, 'Pam Blondi', 'pam@gmail.com', 'Thank you. I&apos;ll see you soon.', 1745708281, 0),
(4, 'Y&apos;all', 'yall@gmail.com', 'Testing Apostrophes here &apos;n there.', 1745865759, 0),
(5, 'Marc Rewbio', 'marc@gmail.com', 'alert(&apos;Hello, I&apos;m malicious!&apos;);', 1746142658, 0),
(6, 'Pete Hackseth', 'pete@gmail.com', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 1746648757, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `template` varchar(50) DEFAULT 'default',
  `published` int(1) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `menus`
--

INSERT INTO `menus` (`id`, `name`, `description`, `template`, `published`, `date_created`, `date_updated`) VALUES
(1, 'Main Navigation', 'The Main Menu of the Website, located at the top.', 'default_2', 1, '2025-03-30 17:59:33', '2025-03-31 00:51:21'),
(2, 'Footer Menu', 'Simple footer links', 'footer', 1, '2025-03-30 18:00:08', '2025-03-30 22:18:43'),
(3, 'Backend Menu', 'Admin navigation', 'backend', 1, '2025-03-30 18:00:52', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `url_string` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `published` int(1) DEFAULT NULL,
  `target` varchar(12) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `menus_id` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `menu_items`
--

INSERT INTO `menu_items` (`id`, `title`, `url_string`, `parent_id`, `sort_order`, `published`, `target`, `date_created`, `date_updated`, `menus_id`) VALUES
(1, 'start', 'http://localhost/tinyblog', 0, 1, 1, '_self', '0000-00-00 00:00:00', '2025-04-30 18:09:25', 1),
(2, 'blog', 'blog', 0, 2, 1, '_self', '2025-03-30 18:01:05', '2025-03-30 23:34:24', 1),
(3, 'explore', 'http://localhost/tinyblog/explore', 0, 3, 1, '_self', '2025-03-30 18:01:05', '2025-03-30 23:48:30', 1),
(4, 'contact', 'http://localhost/tinyblog/enquiries', 0, 4, 1, '_self', '2025-03-30 18:01:05', '2025-04-08 09:10:53', 1),
(5, 'Blog', 'http://localhost/tinyblog/blog', 0, 1, 1, '_self', '2025-03-30 22:16:17', '1970-01-01 01:00:00', 2),
(6, 'Explore', 'http://localhost/tinyblog/explore', 0, 2, 1, '_self', '0000-00-00 00:00:00', '2025-05-07 09:44:00', 2),
(7, 'Contact', 'http://localhost/tinyblog/enquiries', 0, 3, 1, '_self', '0000-00-00 00:00:00', '2025-05-07 09:44:15', 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `trongate_administrators`
--

CREATE TABLE `trongate_administrators` (
  `id` int(11) NOT NULL,
  `username` varchar(65) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `trongate_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `trongate_administrators`
--

INSERT INTO `trongate_administrators` (`id`, `username`, `password`, `trongate_user_id`) VALUES
(1, 'admin', '$2y$11$SoHZDvbfLSRHAi3WiKIBiu.tAoi/GCBBO4HRxVX1I3qQkq3wCWfXi', 1),
(2, 'John Rambo', '$2y$11$WqJXyzHcgxXCCsHe6A8Z6u5.b4GhZ4iEQsS5uOf2kc7RrWZ6KZLtq', 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `trongate_comments`
--

CREATE TABLE `trongate_comments` (
  `id` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `date_created` int(11) DEFAULT 0,
  `user_id` int(11) DEFAULT NULL,
  `target_table` varchar(125) DEFAULT NULL,
  `update_id` int(11) DEFAULT NULL,
  `code` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `trongate_comments`
--

INSERT INTO `trongate_comments` (`id`, `comment`, `date_created`, `user_id`, `target_table`, `update_id`, `code`) VALUES
(1, 'Great Post!', 1743891204, 1, 'blog_posts', 9, 'gksXQB'),
(2, 'Escape bitte noch die Names bei den Tags', 1743928631, 1, 'blog_tags', 6, 'XwfK43'),
(3, 'word_count macht Probleme wenn 0 !!', 1744100981, 1, 'blog_posts', 11, 'gFhrps'),
(4, 'Wir testen Kommentare, heute: Source, Owner', 1745683260, 1, 'blog_sources', 1, 'kGbEQj');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `trongate_pages`
--

CREATE TABLE `trongate_pages` (
  `id` int(11) NOT NULL,
  `url_string` varchar(255) DEFAULT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `page_body` text DEFAULT NULL,
  `date_created` int(11) DEFAULT NULL,
  `last_updated` int(11) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `trongate_pages`
--

INSERT INTO `trongate_pages` (`id`, `url_string`, `page_title`, `meta_keywords`, `meta_description`, `page_body`, `date_created`, `last_updated`, `published`, `created_by`) VALUES
(1, 'homepage', 'Homepage', '', '', '<div style=\"text-align: center;\">\n    <h1>It Totally Works!</h1>\n</div>\n<div class=\"text-div\">\n    <p>\n        <i>Congratulations!</i> You have successfully installed Trongate.  <b>This is your homepage.</b>  Trongate was built with a focus on lightning-fast performance, while minimizing dependencies on third-party libraries. By adopting this approach, Trongate delivers not only exceptional speed but also rock-solid stability.\n    </p>\n    <p>\n        <b>You can change this page and start adding new content through the admin panel.</b>\n    </p>\n</div>\n<h2>Getting Started</h2>\n<div class=\"text-div\">\n    <p>\n        To get started, log into the <a href=\"[website]tg-admin\">admin panel</a>. From the admin panel, you\'ll be able to easily edit <i>this</i> page or create entirely <i>new</i> pages. The default login credentials for the admin panel are as follows:\n    </p>\n    <ul>\n        <li>Username: <b>admin</b></li>\n        <li>Password: <b>admin</b></li>\n    </ul>\n</div>\n<div class=\"button-div\" style=\"cursor: pointer; font-size: 1.2em;\">\n    <div style=\"text-align: center;\">\n        <button onclick=\"window.location=\'[website]tg-admin\';\">Admin Panel</button>\n        <button class=\"alt\" onclick=\"window.location=\'https://trongate.io/docs\';\">Documentation</button>\n    </div>\n</div>\n<h2 class=\"mt-2\">About Trongate</h2>\n<div class=\"text-div\">\n    <p>\n        <a href=\"https://trongate.io/\" target=\"_blank\">Trongate</a> is an open source project, written in PHP. The GitHub repository for Trongate is <a href=\"https://github.com/trongate/trongate-framework\" target=\"_blank\">here</a>. Contributions are welcome! If you\'re interested in learning how to build custom web applications with Trongate, a good place to start is The Learning Zone. The URL for the Learning Zone is: <a href=\"https://trongate.io/learning-zone\" target=\"_blank\">https://trongate.io/learning-zone</a>. <b>If you enjoy working with Trongate, all we ask is that you give Trongate a star on <a href=\"https://github.com/trongate/trongate-framework\" target=\"_blank\">GitHub</a>.</b> It really helps!\n    </p>\n    <p>\n        Finally, if you run into any issues or you require technical assistance, please do visit our free Help Bar, which is at: <a href=\"https://trongate.io/help_bar\" target=\"_blank\">https://trongate.io/help_bar</a>.\n    </p>\n</div>', 1723807486, 1746463892, 1, 1),
(2, 'explore', 'Explore', '', '', '<h1>Explore</h1><div class=\"text-div\"><p>Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.</p><p>Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.</p></div>', 1742670705, 1746469815, 1, 1),
(3, 'contact', 'Contact', '', '', '<h1>Contact</h1><div class=\"text-div\"><p>Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.</p></div><div class=\"text-div\"><p>Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis. Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Sit sint perferendis a totam repellendus vitae architecto sunt obcaecati doloribus deserunt, unde, molestiae maxime. Enim adipisci officiis sit. Quasi, aliquam, facilis.Lorem ipsum, dolor sit amet, consectetur adipisicing elit.</p></div>', 1743371199, 1746463774, 0, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `trongate_tokens`
--

CREATE TABLE `trongate_tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(125) DEFAULT NULL,
  `user_id` int(11) DEFAULT 0,
  `expiry_date` int(11) DEFAULT NULL,
  `code` varchar(3) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `trongate_tokens`
--

INSERT INTO `trongate_tokens` (`id`, `token`, `user_id`, `expiry_date`, `code`) VALUES
(167, '2nWamPhe4pqfjz3L6PQgtBUTx3Ww6LHW', 1, 1746692021, '0');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `trongate_users`
--

CREATE TABLE `trongate_users` (
  `id` int(11) NOT NULL,
  `code` varchar(32) DEFAULT NULL,
  `user_level_id` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `trongate_users`
--

INSERT INTO `trongate_users` (`id`, `code`, `user_level_id`) VALUES
(1, 'Tz8tehsWsTPUHEtzfbYjXzaKNqLmfAUz', 1),
(2, 'FJcbwP2JDkKHPV6H2A6Tvp7cbEuXEwLk', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `trongate_user_levels`
--

CREATE TABLE `trongate_user_levels` (
  `id` int(11) NOT NULL,
  `level_title` varchar(125) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `trongate_user_levels`
--

INSERT INTO `trongate_user_levels` (`id`, `level_title`) VALUES
(1, 'admin');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `associated_blog_tags_and_blog_posts`
--
ALTER TABLE `associated_blog_tags_and_blog_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `blog_pictures`
--
ALTER TABLE `blog_pictures`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `blog_sources`
--
ALTER TABLE `blog_sources`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `blog_tags`
--
ALTER TABLE `blog_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `trongate_administrators`
--
ALTER TABLE `trongate_administrators`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `trongate_comments`
--
ALTER TABLE `trongate_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `trongate_pages`
--
ALTER TABLE `trongate_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `trongate_tokens`
--
ALTER TABLE `trongate_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `trongate_users`
--
ALTER TABLE `trongate_users`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `trongate_user_levels`
--
ALTER TABLE `trongate_user_levels`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `associated_blog_tags_and_blog_posts`
--
ALTER TABLE `associated_blog_tags_and_blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT für Tabelle `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `blog_pictures`
--
ALTER TABLE `blog_pictures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT für Tabelle `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT für Tabelle `blog_sources`
--
ALTER TABLE `blog_sources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT für Tabelle `blog_tags`
--
ALTER TABLE `blog_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `trongate_administrators`
--
ALTER TABLE `trongate_administrators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `trongate_comments`
--
ALTER TABLE `trongate_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `trongate_pages`
--
ALTER TABLE `trongate_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `trongate_tokens`
--
ALTER TABLE `trongate_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT für Tabelle `trongate_users`
--
ALTER TABLE `trongate_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `trongate_user_levels`
--
ALTER TABLE `trongate_user_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
