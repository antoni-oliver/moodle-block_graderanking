# graderanking
This simple plugin adds a block that displays a ranking of a grade category.

Multiple instances of this block can be displayed in a single course.

## About displaying rankings of grades...
Although the idea is to promote the students to complete their tasks, if these are part of the final grade of the subject, the effect may be the opposite and, furthermore, a list of (part of) the grades of the subject would be made public, which may not be desirable.

It is advisable to use this block for tasks that are optional, not part of the final grade, part of a gamification program or other things alike.

# Setup

## Setting up the plugin
Just install the plugin as usual.

## Setting up a grade category
The first step would be to create a [grade category](https://docs.moodle.org/en/Grade_categories) in standard Moodle.

Grade categories aggregate grades from other sources, such as quizzes. You may use the average of the grades or their natural sum. You can also create a category from a single item.

## Setting up graderanking
Insert the block as usual and then configure it by specifying these options:

* **Block title**: this is the title of the block that will be displayed.
* **Grade category**: this is the grade category which will be used to create the ranking, as discussed above.
* **Decimal digits**: how many decimal digits should be displayed.
* **Grade name**: the name that will be displayed for the grade.
* **Table height (px)**: height of the table in pixels, so it will display a scroll bar if there are more students. Leave "0" to make the table as long as students there are.

# An use case

![The grade blocks](https://i.imgur.com/sHA7pZw.png)

Two grade categories were created in this sample course:
* **Completed exercises**: multiple quizzes are created with several exercises in each. All exercises are worth one point and the category is aggregated with Natural aggregation (sum of grades).
Students can see if they are behind their colleagues in the practice of the course.
* **Gamification points**: multiple quizzes are created with some exercises in each. Each exercise is worth an amount of points and the category is aggregated with Natural aggregation (sum of grades). Other elements, such as forum participation, can be included in the category.
Students can check their gamification points and their colleagues'.

# Privacy
The grade ranking block only displays existing grade data.