# Codeable Reviews and Expert Profile Plugin

WordPress plugin for front end display of Codeable expert information

Stores expert and review information from the API in transients to lower number of server calls
* Expert Information (1 day)
* Reviews (4 hours)

## Shortcodes (codeable_id att required):

| Shortcodes       | Item to Insert       |
|:------------- |:-------------|
| [expert_reviews codeable_id=31044] | shows reviews |
| [expert_image codeable_id=31044] | profile image |
| [expert_completed codeable_id=31044] | # of completed projects |
| [expert_rating codeable_id=31044] | average rating |
| [expert_hire codeable_id=31044] | button to hire |

## Optional Atts
### Optional atts: expert_reviews
* number_to_show, defaults to 4, how many to show
* show_title=yes to show task title, defaults to no
* show_date=yes to show review date, defaults to no
* min_score, only shows reviews above and including this score (blank is no min)
* max_score, only shows reviews below and including this score (blank is no max)
* sort=rand, sorting options, valid value is just rand for now. Default is no sorting (profile page order)

### Optional atts: expert_image
* circle=yes , default is yes shows image as a circle
* class=your-class , add a custom extra class to the image tag for easier styling

### Optional atts: expert_hire
* message="Your Message", defaults to "Hire Me"
* referoo=xxxxxx, defaults to empty
* class=your-class, optional extra class to add to the link to make styling easier
* theme=black sets a button theme: valid values are black, white, or anything else for no theme (defaults to black)

### No optional atts on expert_completed or expert_rating
