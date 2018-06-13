package com.company;

import java.util.*; 

public class StudentVersionHT {
	private String mName;
    private String mMajor;
    // Utilizing the Course Number, since a course could be taken multiple times, yet the number should be a unique identifier, at least here at ESC it appears to be
    private Hashtable<Integer, Course> mCourseHash; 
    public StudentVersionHT(String name, String major){
        mName = name;
        Course.isValidAreaOfStudy(major);
        mMajor = major;
        mCourseHash = new Hashtable<>();
    }

    public boolean put(Course course){
        // TODO this method adds a Grade object to your collection
    	if (!mCourseHash.containsKey(course.getCourseNumber())) {   
    		mCourseHash.put(course.getCourseNumber(), course);
    		return true;
    	}
    	else
    		return false;
    }

    
    public boolean tookThisCourse(String courseName){
        // TODO returns bool representing if this student took this Course 
    	// Course number might be better to use here, since that is the key
    	// A need to iterate through a list of the table will be inherent 
    	for (int key: mCourseHash.keySet()) {
    		if (mCourseHash.get(key).getCourseName() == courseName)
    			return true;
    	}
  		return false;
    }

    public double getGPA(){
        // TODO returns student GPA based on collection of Course objects
    	double score = 0.0;
    	double credits = 0.0;
    	for(int key: mCourseHash.keySet()) {
    		score += mCourseHash.get(key).getWeightedValue();
            credits += mCourseHash.get(key).getNumberOfCredits();
    	}
        return score / credits;
    }

    public Course getCourse(String courseName){
        // TODO returns the appropriate Course node
    	for (int key: mCourseHash.keySet()) {
    		if (mCourseHash.get(key).getCourseName() == courseName)
    			return mCourseHash.get(key);
    	}
        return null;
    }

    public boolean eligibleToGraduate(){
        /** TODO returns bool representing if student has met requirements to graduate(we will define based on some
         * quantity of credits, some quantity of upper level credits, and quantity of credits in the three areaOfStudy*/
        // Must have 36 credits in major
        if (!hasCompletedMajorRequirement()){
            return false;
        }
        // Must have 120 credits total
        if (getCreditCount() < 120){
            return false;
        }
        // Must have 60 upper level credits
        if (!hasCompletedUpperLevelRequirement()){
            return false;
        }
        return true;
    }
    
    public int getCountMajorCredits(){
        // Returns int count of credits completed in student's major
        int credits = 0;
        for (int key: mCourseHash.keySet()){
            if (mCourseHash.get(key).getAreaOfStudy().equals(mMajor)){
                credits += mCourseHash.get(key).getNumberOfCredits();
            }
        }
        return credits;
    }

    public boolean hasCompletedMajorRequirement(){
        // Returns bool representing if student has >= 36 credits in their major
        int credits = getCountMajorCredits();
        if (credits >= 36){
            return true;
        } else {
            return false;
        }
    }


    public int getCreditCount(){
        // TODO returns total number of credits the student has
    	int credits = 0;
    	for (int key: mCourseHash.keySet()) {
    		credits += mCourseHash.get(key).getNumberOfCredits();
    	}
        return credits;
    }
    
    public int getCountUpperLevelCredits(){
        // Returns int count of upper-level credits
        int credits = 0;
        for (int key: mCourseHash.keySet()){
            if (mCourseHash.get(key).isUpperLevel()){
                credits += mCourseHash.get(key).getNumberOfCredits();
            }
        }
        return credits;
    }

    public boolean hasCompletedUpperLevelRequirement(){
        // Returns bool if student has completed >= 60 credits of upper-level study
        int credits = getCountUpperLevelCredits();
        if (credits >= 60){
            return true;
        } else {
            return false;
        }
    }

    public String getName(){
        // Getter method for private String mName
        return mName;
}
}
