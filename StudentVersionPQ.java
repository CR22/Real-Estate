package com.company;

import java.util.*;

public class StudentVersionPQ implements Comparable<StudentVersionPQ>  {
	private String mName;
	private String mMajor;
	private PriorityQueue<Course> pq1;

	public StudentVersionPQ(String name, String major) {
		mName = name;
		Course.isValidAreaOfStudy(major);
		mMajor = major;
		pq1 = new PriorityQueue<Course>();
	}

	/**
	 * Adds the course taken by a student into the PriorityQueue.
	 * 
	 * @param course
	 */
	public void put(Course course) {
		pq1.add(course);
	}

	/**
	 * Checks if a course was taken by a student.
	 * 
	 * @param courseName
	 * @return True if the course was taken, false if the course was not taken.
	 */
	public boolean tookThisCourse(String courseName) {

		for (Course course : pq1) {
			if (course.getCourseName().equals(courseName)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Calculates the students GPA from a collection of Grade object.
	 * 
	 * @return The GPA of the student.
	 */
	public double getGPA() {

		double score = 0.0;
		double credits = 0;
		for (Course course : pq1) {
			score += course.getWeightedValue();
			credits += course.getNumberOfCredits();
		}
		return score / credits;
	}

	/**
	 * Gets the name of the course
	 * 
	 * @param courseName
	 * @return The applicable grade node
	 * @throws IllegalArgumentException if the course is not found.
	 */
	public Course getCourse(String courseName) {

		for (Course course : pq1) {
			if (course.getCourseName().equals(courseName)) {
				return course;
			}
		}
		String message = courseName + " is not found.";
		throw new IllegalArgumentException(message);
	}

	/**
	 * Must have 36 credits in major Must have 60 upper level credits Must have 120
	 * credits total
	 * 
	 * @return A bool representing if the student has met requirements to graduate
	 */
	public boolean eligibleToGraduate() {

		if (!hasCompletedMajorRequirements()) {
			System.out.println("Student does not have enough major credits to graduate.");
			return false;
		}
		if (getCreditCount() < 120) {
			return false;
		}
		if (!hasCompletedUpperLevelRequirements()) {
			System.out.println("Student does not have enough upper level credits to graduate.");
			return false;
		}

		return true;
	}

	/**
	 * 
	 * Counts the number of credits in the students major.
	 * 
	 * @return An int count of credits completed in students major
	 */

	public int getCountMajorCredits() {
		
		int credits = 0;
		for (Course course : pq1) {
			if (course.getAreaOfStudy().equals(mMajor)) {
				credits += course.getNumberOfCredits();
			}
		}
		return credits;
	}

	/**
	 * A helper function for method eligibleToGraduate().
	 * 
	 * @return A bool representing if the student has enough credits in a given area
	 */
	public boolean hasCompletedMajorRequirements() {

		int credits = getCountMajorCredits();
		if (credits >= 36)
			return true;
		else
			return false;
	}

	/**
	 * Counts the number of credits for all courses taken by a student.
	 * 
	 * @return The total number of credits.
	 * 
	 */
	public int getCreditCount() {

		int credits = 0;
		for (Course course : pq1) {
			credits += course.getNumberOfCredits();
		}
		return credits;
	}


	public int getCountUpperLevelCredits() {

		int credits = 0;
		for (Course course : pq1) {
			if (course.isUpperLevel()) {
				credits += course.getNumberOfCredits();
			}
		}
		return credits;
	}

	public boolean hasCompletedUpperLevelRequirements() {
		int credits = getCountUpperLevelCredits();
		if (credits >= 60)
			return true;
		else
			return false;
	}
	
	public String toString() {
		return mName + " " + getCreditCount();
	}

	public String getName() {
		return mName;
	}
	
    /**
     * Compares number of course credits taken by a student.
     *@return credits in increasing / decreasing order
     * 
     */
	public int compareTo (StudentVersionPQ otherStudentVersionPQ) {
		
		if (getCreditCount() < otherStudentVersionPQ.getCreditCount())
			return 1;
			//return -1;
		if (getCreditCount()> otherStudentVersionPQ.getCreditCount())
			return -1;
		    //return 1;
		return 0;
	} // method compareTo
}