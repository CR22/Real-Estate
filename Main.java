package com.company;

public class Main {

    public static void main(String[] args) {
        StudentVersionOne s1 = new StudentVersionOne("John","CS");
	    s1.put(new Course("Course1", 100, 4, "A", "CS"));
        s1.put(new Course("Course2", 100, 3, "B+", "CS"));
        s1.put(new Course("Course3", 100, 2, "C", "CS"));
        s1.put(new Course("Course4", 100, 1, "D+", "CS"));
        s1.put(new Course("Course5", 100, 1, "D+", "Math"));
        System.out.println(s1.getGPA());
//        System.out.println(s1.getCourse("Course1"));
//        System.out.println("Course taken? " + s1.tookThisCourse("Course1"));
        System.out.println("Total number of credits " + s1.getCreditCount());
        System.out.println(s1.hasCompletedUpperLevelRequirements());
        System.out.println("Eligible to graduate: " + s1.eligibleToGraduate());

    }

}